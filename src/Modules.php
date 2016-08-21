<?php
// Jivoo Core 
// Copyright (c) 2016 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo;

/**
 * A collection of application submodules.
 */
class Modules
{
    
    /**
     * Mapping from module names to class names.
     *
     * @var string[]
     */
    protected $types = [];
    
    /**
     * @var object[]
     */
    private $instances = [];
    
    /**
     * @var callable
     */
    private $autoloader = null;
    
    /**
     * Get a loaded module.
     *
     * @param string $name Module name.
     * @return object Module instance.
     * @throws InvalidModuleException If module is undefined.
     */
    public function __get($name)
    {
        if (! isset($this->instances[$name])) {
            if (! isset($this->autoloader)) {
                throw new InvalidModuleException('Undefined module: ' . $name);
            }
            $this->instances[$name] = call_user_func($this->autoloader, $name);
            if (! isset($this->instances[$name])) {
                throw new InvalidModuleException('Unable to load module: ' . $name);
            }
        }
        return $this->instances[$name];
    }
    
    /**
     * Whether a module exists.
     *
     * @param string $name Module name.
     * @return boolean True if the module exists, false otherwise.
     */
    public function __isset($name)
    {
        if (isset($this->instances[$name])) {
            return true;
        } elseif (isset($this->autoloader)) {
            try {
                $this->$name;
                return true;
            } catch (InvalidModuleException $e) {
            }
        }
        return false;
    }
    
    /**
     * Set module instance.
     *
     * @param string $name Module name.
     * @param object $instance Module instance.
     * @throws InvalidModuleException If a module with that name already exists
     * and the new instance is incompatible.
     */
    public function __set($name, $instance)
    {
        if (isset($this->types[$name])) {
            if (!Utilities::isSubclassOf($instance, $this->types[$name])) {
                throw new InvalidModuleException(
                    'The module "' . $name . '" is expected to be an instance of '
                    . $this->types[$name]
                );
            }
        }
        $this->instances[$name] = $instance;
        if (! isset($this->types[$name])) {
            $this->types[$name] = get_class($instance);
        }
    }
    
    /**
     * Set module autoloader.
     *
     * @param callable $autoloader A function that accepts a module name and
     * returns a module instance, or null. The function may also throw
     * a {@see InvalidModuleException}.
     */
    public function setAutoloader(callable $autoloader)
    {
        $this->autoloader = $autoloader;
    }
    
    /**
     * Get the current module autoloader if any.
     *
     * @return callable|null The autoloader function.
     */
    public function getAutoloader()
    {
        return $this->autoloader;
    }
    
    /**
     * Expect a module to be loaded.
     *
     * @param sring $name Module name.
     * @param string $class Optional expected class of module instance.
     * @throws InvalidModuleException If the module doesn't exist, or has the
     * wrong type.
     */
    public function required($name, $class = null)
    {
        if (! isset($this->$name)) {
            throw new InvalidModuleException('Undefined module: ' . $name . ', required by ' . Utilities::getCaller());
        }
        if (isset($class) and !Utilities::isSubclassOf($this->$name, $class)) {
            throw new InvalidModuleException(
                'The module "' . $name . '" of type ' . get_class($this->$name)
                . ' was expected by ' . Utilities::getCaller()
                . ' to be of type ' . $class
            );
        }
    }
    
    /**
     * Like {@see required}, but only checks the type of the module if the
     * module exists.
     *
     * @param sring $name Module name.
     * @param string $class Expected class of module instance.
     * @throws InvalidModuleException If the module has the wrong type.
     */
    public function optional($name, $class)
    {
        if (isset($this->$name) and !Utilities::isSubclassOf($this->$name, $class)) {
            throw new InvalidModuleException(
                'The module "' . $name . '" of type ' . get_class($this->$name)
                . ' was expected by ' . Utilities::getCaller()
                . ' to be of type ' . $class
            );
        }
    }
}
