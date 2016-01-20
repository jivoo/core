<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo\I18n;

use Jivoo\Exception;

/**
 * Thrown when a locale file is unreadable.
 */
class LocaleException extends \UnexpectedValueException
  implements Exception {}
