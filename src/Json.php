<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo;

/**
 * JSON encoding and decoding.
 * @TODO Fallback when json php extension is missing.
 */
class Json
{

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Encode a value as JSON.
     *
     * @param mixed $object
     *            Any object.
     * @return string JSON.
     */
    public static function encode($object)
    {
        return json_encode($object);
    }

    /**
     * Pretty print a value as JSON.
     *
     * @param mixed $object
     *            Any object.
     * @return string JSON.
     */
    public static function prettyPrint($object)
    {
        return json_encode($object, JSON_PRETTY_PRINT);
    }

    /**
     * Decode a JSON string.
     *
     * @param string $json
     *            JSON.
     * @return mixed Decoded JSON.
     * @throws JsonException If decoding fails.
     */
    public static function decode($json)
    {
        $object = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = 'JSON error';
            if (function_exists('json_last_error_msg')) {
                $error = json_last_error_msg();
            } else { // @codeCoverageIgnoreStart
                switch (json_last_error()) {
                    case JSON_ERROR_DEPTH:
                        $error = 'maximum stack depth exceeded';
                        break;
                    case JSON_ERROR_STATE_MISMATCH:
                        $error = 'invalid or malformed JSON';
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        $error = 'control character error';
                        break;
                    case JSON_ERROR_SYNTAX:
                        $error = 'JSON syntax error';
                        break;
                    case JSON_ERROR_UTF8:
                        $error = 'malformed UTF-8 characters';
                        break;
                }
            } // @codeCoverageIgnoreEnd
            throw new JsonException($error);
        }
        return $object;
    }

    /**
     * Decode a file as JSON.
     *
     * @param string $file
     *            File path.
     * @return mixed Decoded JSON.
     */
    public static function decodeFile($file)
    {
        return self::decode(file_get_contents($file));
    }
}
