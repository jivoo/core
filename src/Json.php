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
            throw new JsonException(json_last_error_msg());
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
