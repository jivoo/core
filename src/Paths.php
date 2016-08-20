<?php

// Jivoo Core 
// Copyright (c) 2016 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo;

/**
 * A collection of paths to different parts of an application. Use {@see p} to
 * decode an internal path.
 */
class Paths implements \ArrayAccess
{

    /**
     * @var string[]
     */
    private $paths = array();

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string
     */
    private $defaultPath;

    /**
     * Construct path collection.
     * @param string $basePath Base path for all relative paths added using
     * {@see __set}.
     * @param string $defaultPath If defined, this will be used instead of
     * $basePath when undefined paths are requested with {@see __get}.
     */
    public function __construct($basePath, $defaultPath = null)
    {
        $this->basePath = $basePath;
        $this->defaultPath = $this->basePath;
        if (isset($defaultPath)) {
            $this->defaultPath = $defaultPath;
        }
    }

    /**
     * Get the path associated with a key.
     *
     * If the key is undefined, the key will be appended to the default path.
     *
     * If the associated path is absolute, e.g. begins with '/' or 'C:/' then
     * only the path is returned, otherwise the path is appended to the base path.
     * @param string $key Path key.
     * @return string Path.
     */
    public function __get($key)
    {
        if (!isset($this->paths[$key])) {
            return self::combinePaths($this->defaultPath, $key);
        }
        return $this->paths[$key];
    }

    /**
     * Associate a path with a key. Automatically appends {@see $basePath} to
     * relative paths.
     * @param string $key Path key.
     * @param string $path Path.
     */
    public function __set($key, $path)
    {
        $path = Utilities::convertPath($path);
        if (Utilities::isAbsolutePath($path)) {
            $this->paths[$key] = $path;
        } else {
            $this->paths[$key] = self::combinePaths($this->basePath, $path);
        }
    }

    /**
     * Delete a path.
     * @param string $key Path key.
     */
    public function __unset($key)
    {
        if (isset($this->paths[$key])) {
            unset($this->paths[$key]);
        }
    }

    /**
     * Whether a path exists in collection.
     * @param string $key Path key.
     * @return bool True if a path exists with that key.
     */
    public function __isset($key)
    {
        return isset($this->paths[$key]);
    }

    /**
     * Convert an internal path.
     * @param string $ipath Internal path, e.g. 'key/followed/by/subpath'.
     * @param string $context Optional context for relative paths (paths starting
     * with './'). The default is the {@see $basePath}.
     */
    public function p($ipath, $context = null)
    {
        $ipath = Utilities::convertPath($ipath);
        if (!isset($context)) {
            $context = $this->basePath;
        }
        if ($ipath == '') {
            return $context;
        }
        $splits = explode('/', $ipath, 2);
        $key = $splits[0];
        $path = '';
        if (isset($splits[1])) {
            $path = $splits[1];
        }
        if ($key == '.') {
            return self::combinePaths($context, $path);
        }
        return self::combinePaths($this->__get($key), $path);
    }

    /**
     * Whether the path exists.
     * @param string $ipath Internal path.
     * @return bool True if path exists.
     */
    public function fileExists($ipath)
    {
        return file_exists($this->p($ipath));
    }

    /**
     * Whether the path exists and is a directory.
     * @param string $ipath Internal path.
     * @return bool True if path exists and is a directory.
     */
    public function isDir($ipath)
    {
        return is_dir($this->p($ipath));
    }

    /**
     * Check whether a directory exists or create it if it doesn't.
     * @param string $ipath Internal path.
     * @param bool $create Attempt to create directory if it doesn't exist.
     * @param bool $recursive Whether to recursively create parent directories
     * as well.
     * @param int $mode Directory permission, default is 0777.
     * @return bool True if directory exists.
     */
    public function dirExists($ipath, $create = true, $recursive = true, $mode = 0777)
    {
        $path = $this->p($ipath);
        return Utilities::dirExists($path, $create, $recursive, $mode);
    }

    /**
     * Combine two paths with a path separator ('/').
     * @param string $pathA First path.
     * @param string $pathB Second path.
     * @return string Combined path.
     */
    public static function combinePaths($pathA, $pathB)
    {
        if ($pathB == '' or $pathB == '/') {
            return $pathA;
        }
        if ($pathA == '') {
            return $pathB;
        }
        if ($pathA == '/') {
            $pathA = '';
        }
        return $pathA . '/' . $pathB;
    }

    /**
     * Get the path associated with a key. See {@see __get}.
     * @param string $key Path key.
     * @return string Path.
     */
    public function offsetGet($key)
    {
        return $this->__get($key);
    }

    /**
     * Associate a path with a key. See {@see __set}.
     * @param string $key Path key.
     * @param string $path Path.
     */
    public function offsetSet($key, $path)
    {
        if (isset($key)) {
            $this->__set($key, $path);
        }
    }

    /**
     * Delete a path.
     * @param string $key Path key.
     */
    public function offsetUnset($key)
    {
        $this->__unset($key);
    }

    /**
     * Whether a path exists in collection.
     * @param string $key Path key.
     * @return bool True if a path exists with that key.
     */
    public function offsetExists($key)
    {
        return $this->__isset($key);
    }
}
