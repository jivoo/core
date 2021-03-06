<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo\Parse;

use Jivoo\Exception;

/**
 * Thrown when a log level is undefined.
 */
class ParseException extends \UnexpectedValueException implements Exception
{
}
