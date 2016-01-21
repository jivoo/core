<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo;

/**
 * Collection of useful utility functions.
 */
class Utilities
{

    private function __construct()
    {
    }

    /**
     * Convert path from Windows-style to UNIX-style.
     *
     * @param string $path
     *            Windows-style path.
     * @return string UNIX-style path.
     */
    public static function convertPath($path)
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * Convert a real path from Windows-style to UNIX-style.
     * Uses
     * {@see realpath) to look up the path.
     *
     * @param string $path
     *            Windows-style path.
     * @return string UNIX-style path.
     */
    public static function convertRealPath($path)
    {
        return str_replace('\\', '/', realpath($path));
    }

    /**
     * Convert a CamelCase class-name to a lowercase dash-separated name.
     * E.g.
     * from "CamelCase" to "camel-case". Also known as "lisp-case"
     *
     * @param string $camelCase
     *            A camel case string.
     * @return string Dash-separated string.
     */
    public static function camelCaseToDashes($camelCase)
    {
        $dashes = preg_replace('/([A-Z])/', '-$1', lcfirst($camelCase));
        return strtolower($dashes);
    }

    /**
     * Convert a CamelCase class-name to a lowercase underscore-separated name.
     * E.g. from "CamelCase" to "camel_case". Also known as "snake_case".
     *
     * @param string $camelCase
     *            A camel case string.
     * @return string Uderscore-separated string.
     */
    public static function camelCaseToUnderscores($camelCase)
    {
        $underscores = preg_replace('/([A-Z])/', '_$1', lcfirst($camelCase));
        return strtolower($underscores);
    }

    /**
     * Convert a lowercase dash-separated name to a camel case class-name.
     * E.g.
     * from "camel-case" to "CamelCase".
     *
     * @param string $dashes
     *            Dash-separated string
     * @return string A camel case string
     */
    public static function dashesToCamelCase($dashes)
    {
        $words = explode('-', $dashes);
        $camelCase = '';
        foreach ($words as $word) {
            $camelCase .= ucfirst($word);
        }
        return $camelCase;
    }

    /**
     * Convert a lowercase underscore-separated name to a camel case class-name.
     * E.g. from "camel_case" to "CamelCase".
     *
     * @param string $underscores
     *            Underscores-separated string
     * @return string A camel case string
     */
    public static function underscoresToCamelCase($underscores)
    {
        $words = explode('_', $underscores);
        $camelCase = '';
        foreach ($words as $word) {
            $camelCase .= ucfirst($word);
        }
        return $camelCase;
    }

    /**
     * Create slug style string from any string.
     * @TODO Unicode support?
     *
     * @param string $string
     *            String.
     * @return string Slug.
     */
    public static function stringToDashes($string)
    {
        return preg_replace('/ /', '-', preg_replace('/[^a-z -]/', '', strtolower($string)));
    }

    /**
     * Get namespace part of a class name.
     *
     * @param string|object $className
     *            Class or object, e.g. 'Jivoo\Utilities'.
     * @return string Namespace, e.g. 'Jivoo'.
     */
    public static function getNamespace($className)
    {
        if (is_object($className)) {
            $className = get_class($className);
        }
        if (strpos($className, '\\') === false) {
            return '';
        }
        return preg_replace('/\\\\[^\\\\]+$/', '', $className);
    }

    /**
     * Get class name part of a qualified class name.
     *
     * @param string|object $className
     *            Class or object, e.g. 'Jivoo\Utilities'.
     * @return string Class name, e.g. 'Utilities'.
     */
    public static function getClassName($className)
    {
        if (is_object($className)) {
            $className = get_class($className);
        }
        $className = array_slice(explode('\\', $className), - 1);
        return $className[0];
    }

    /**
     * Check whether a directory exists or create it if it doesn't.
     *
     * @param string $file
     *            File path.
     * @param bool $create
     *            Attempt to create directory if it doesn't exist.
     * @param bool $recursive
     *            Whether to recursively create parent directories
     *            as well.
     * @param int $mode
     *            Directory permission, default is 0777.
     * @return bool True if directory exists.
     */
    public static function dirExists($file, $create = true, $recursive = true, $mode = 0777)
    {
        return is_dir($file) or ($create and mkdir($file, $mode, $recursive));
    }

    /**
     * Get lower case file extension from file name.
     *
     * @param string $file
     *            File name.
     * @return string File extension.
     */
    public static function getFileExtension($file)
    {
        $array = explode('?', $file);
        $array = explode('.', $array[0]);
        return strtolower(array_pop($array));
    }

    /**
     * Whether a path is absolute, e.g.
     * it starts with a slash.
     *
     * @param string $path
     *            Path.
     * @return bool True if absolute, false if relative.
     */
    public static function isAbsolutePath($path)
    {
        if (isset($path[0]) and ($path[0] == '/' or $path[0] == '\\')) {
            return true;
        }
        if (preg_match('/^[A-Za-z0-9]+:/', $path) === 1) {
            return true;
        }
        return false;
    }

    /**
     * Comparison function for use with usort() and uasort() to sort
     * associative arrays with a 'priority'-key.
     *
     * @param array $a
     *            First.
     * @param array $b
     *            Second.
     * @return int Difference.
     */
    public static function prioritySorter($a, $b)
    {
        return $b['priority'] - $a['priority'];
    }
}
