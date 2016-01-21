<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo\Log;

use Jivoo\Exception;

/**
 * Thrown when a PHP error is caught.
 */
class ErrorException extends \ErrorException implements Exception
{
}
