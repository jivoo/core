<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo\Store;

/**
 * Thrown when a state has already been closed (or hasn't been opened).
 */
class NotOpenException extends \DomainException implements StoreException
{
}
