<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo\Cli;

use Jivoo\Log\ErrorHandler;
use Jivoo\Log\FileHandler;
use Psr\Log\LogLevel;
use Jivoo\Log\StreamHandler;
use Jivoo\Log\ShellHandler;
use Jivoo\I18n\I18n;

/**
 * Command-line interface for Jivoo applications.
 */
class Shell extends CommandBase
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var Exception|null
     */
    private $lastError = null;

    /**
     * @var array
     */
    private $options = array();

    private $prompt;

    public function __construct($prompt = '>')
    {
        $this->prompt = $prompt;
        $this->addCommand('help', array(
            $this,
            'showHelp'
        ), I18n::get('Show this help'));
        $this->addCommand('trace', array(
            $this,
            'showTrace'
        ), I18n::get('Show stack trace for most recent exception'));
        $this->addCommand('exit', array(
            $this,
            'stop'
        ), I18n::get('Ends the shell session'));
        $this->addOption('help', 'h');
        $this->addOption('trace', 't');
        $this->addOption('debug', 'd');
    }

    public function parseArguments()
    {
        global $argv;
        $this->name = array_shift($argv);
        
        $command = array();
        
        $option = null;
        
        foreach ($argv as $arg) {
            if (preg_match('/^--(.*)$/', $arg, $matches) === 1) {
                $o = $matches[1];
                if ($o == '') {
                    continue;
                }
                if (! isset($this->availableOptions[$o])) {
                    $this->put(I18n::get('Unknown option: %1', '--' . $o));
                    $this->stop();
                }
                if ($this->availableOptions[$o]) {
                    $option = $o;
                } else {
                    $this->options[$o] = true;
                }
            } elseif (preg_match('/^-(.+)$/', $arg, $matches) === 1) {
                $options = $matches[1];
                while ($options != '') {
                    $o = $options[0];
                    if (! isset($this->shortOptions[$o])) {
                        $this->put(I18n::get('Unknown option: %1', '-' . $o));
                        $this->stop();
                    }
                    $options = substr($options, 1);
                    $o = $this->shortOptions[$o];
                    if ($this->availableOptions[$o]) {
                        if ($options == '') {
                            $option = $o;
                        } else {
                            $this->options[$o] = $options;
                        }
                        break;
                    } else {
                        $this->options[$o] = true;
                    }
                }
            } elseif (isset($option)) {
                $this->options[$option] = $arg;
                $option = null;
            } else {
                $command[] = $arg;
            }
        }
        if (isset($this->options['help'])) {
            $this->showHelp();
            exit();
        }
        if (count($command)) {
            $this->evalCommand($command);
            $this->stop();
        }
    }

    public function evalCommand($command)
    {
        if (is_string($command)) {
            $parameters = explode(' ', $command); // TODO: use regex
        } else {
            $parameters = $command;
        }
        $command = array_shift($parameters);
        if ($command == 'exit') {
            $this->stop();
        }
        if (! isset($this->commands[$command])) {
            $this->error(I18n::get('Unknown command: %1', $command));
            $best = null;
            $bestDist = PHP_INT_MAX;
            foreach ($this->commands as $name => $c) {
                $dist = levenshtein($command, $name);
                if ($dist < $bestDist) {
                    $best = $name;
                    $bestDist = $dist;
                }
            }
            if ($bestDist < 5) {
                $this->put(I18n::get('Did you mean "%1"?', $best));
            }
            return;
        }
        try {
            call_user_func($this->commands[$command], $parameters, $this->options);
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    public function autoComplete($command)
    {
        $length = strlen($command);
        $results = array();
        foreach ($this->commands as $name => $c) {
            if (strncmp($command, $name, $length) == 0) {
                $results[] = $name;
            }
        }
        return $results;
    }

    public function showTrace()
    {
        if (! isset($this->lastError)) {
            return;
        }
        self::dumpException($this->lastError);
    }

    public static function dumpException(\Exception $exception, $stream = STDERR)
    {
        if ($exception instanceof \ErrorException) {
            $title = 'Fatal error (' . ErrorHandler::toString($exception->getSeverity()) . ')';
        } else {
            $title = get_class($exception);
        }
        fwrite(
            $stream,
            $title . ': ' . $exception->getMessage()
                . ' in ' . $exception->getFile() . ':'
                . $exception->getLine() . PHP_EOL . PHP_EOL
        );
        fwrite($stream, 'Stack trace:' . PHP_EOL);
        $trace = $exception->getTrace();
        foreach ($trace as $i => $call) {
            $message = '  ' . sprintf('% 2d', $i) . '. ';
            if (isset($call['file'])) {
                $message .= $call['file'] . ':';
                $message .= $call['line'] . ' ';
            }
            if (isset($call['class'])) {
                $message .= $call['class'] . '::';
            }
            $message .= $call['function'] . '(';
            $arglist = array();
            if (isset($call['args'])) {
                foreach ($call['args'] as $arg) {
                    $arglist[] = (is_scalar($arg) ? var_export($arg, true) : gettype($arg));
                }
                $message .= implode(', ', $arglist);
            }
            $message .= ')' . PHP_EOL;
            fwrite($stream, $message);
        }
        $previous = $exception->getPrevious();
        if (isset($previous)) {
            fwrite($stream, 'Caused by:' . PHP_EOL);
            self::dumpException($previous);
        }
        fflush($stream);
    }

    public function showHelp()
    {
        $this->put('usage: ' . $this->name . ' [options] [command] [args...]');
        $this->help();
    }

    public function handleException(\Exception $exception)
    {
        $this->lastError = $exception;
        if (isset($this->options['trace'])) {
            $this->error(I18n::get('Uncaught exception'));
            self::dumpException($exception);
        } else {
            $this->error(I18n::get('Uncaught %1: %2', get_class($exception), $exception->getMessage()));
            $this->put();
            $this->put(I18n::get('Call "trace" or run script with the "--trace" option to show stack trace'));
        }
    }

    /**
     * Create a string representation of any PHP value.
     *
     * @param mixed $value
     *            Any value.
     * @return string String representation.
     */
    public function dump($value)
    {
        if (is_object($value)) {
            return get_class($value);
        }
        if (is_resource($value)) {
            return get_resource_type($value);
        }
        return var_export($value, true);
    }

    /**
     * Print a line of text to standard error.
     *
     * @param string $line
     *            Line.
     * @param string $eol
     *            Line ending, set to '' to prevent line break.
     */
    public function error($line, $eol = PHP_EOL)
    {
        fwrite(STDERR, $line . $eol);
        fflush(STDERR);
    }

    /**
     * Print a line of text to standard output.
     *
     * @param string $line
     *            Line.
     * @param string $eol
     *            Line ending, set to '' to prevent line break.
     */
    public function put($line = '', $eol = PHP_EOL)
    {
        echo $line . $eol;
        flush();
        fflush(STDOUT);
    }

    /**
     * Read a line of user input from standard input.
     * Uses {@see readline} if
     * available.
     *
     * @param string $prompt
     *            Optional prompt.
     * @return string User input.
     */
    public function get($prompt = '')
    {
        if (function_exists('readline')) {
            $line = readline($prompt);
            readline_add_history($line);
            return $line;
        }
        $this->put($prompt, '');
        return trim(fgets(STDIN));
    }

    /**
     * Ask for confirmation.
     *
     * @param string $prompt
     *            Question.
     * @param boolean|null $default
     *            Default choice or null for no default.
     * @return boolean True for "yes", false for "no".
     */
    public function confirm($prompt, $default = null)
    {
        $prompt .= ' [';
        $prompt .= $default === true ? 'Y' : 'y';
        $prompt .= '/';
        $prompt .= $default === false ? 'N' : 'n';
        $prompt .= '] ';
        while (true) {
            $input = $this->get(':: ' . $prompt);
            if (! is_string($input)) {
                return false;
            }
            if ($input == '') {
                if (is_bool($default)) {
                    return $default;
                }
                continue;
            }
            $input = strtolower($input);
            if ($input == 'yes' or $input == 'y') {
                return true;
            }
            return false;
        }
    }

    /**
     * Stop shell.
     *
     * @param int $status
     *            Status code, 0 for success.
     */
    public function stop($status = 0)
    {
        exit($status);
    }

    public function run()
    {
        $level = LogLevel::INFO;
        if (isset($this->options['debug'])) {
            $level = LogLevel::DEBUG;
        }
        $prompt = $this->prompt;
        $this->logger->addHandler(new ShellHandler($this, $level));
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        $this->parseArguments();
        if (function_exists('readline_completion_function')) {
            readline_completion_function(array(
                $this,
                'autoComplete'
            ));
        }
        while (true) {
            try {
                $line = $this->get($prompt);
                if (! is_string($line)) {
                    $this->stop();
                    return;
                }
                if ($line === '') {
                    continue;
                }
                if ($line[0] == '!') {
                    $command = substr($line, 1);
                    if ($command == '') {
                        continue;
                    }
                    if ($command[0] == '=') {
                        $command = substr($command, 1);
                        $this->put(' => ' . $this->dump(eval('return ' . $command . ';')));
                    } else {
                        eval($command);
                    }
                } elseif ($line[0] == '$') {
                    $this->put(' => ' . $this->dump(eval('return ' . $line . ';')));
                } else {
                    $this->evalCommand($line);
                }
            } catch (\Exception $e) {
                $this->handleException($e);
            }
        }
    }
}
