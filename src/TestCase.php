<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo;

/**
 * A unit testing base class for use with PHPUnit.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * Assert that an exception is thrown.
     *
     * @param string $expected
     *            Expected exception class or interface.
     * @param callable $callable
     *            Callable that should throw exception.
     */
    protected function assertThrows($expected, $callable, $message = null)
    {
        try {
            $callable();
            if (isset($message)) {
                $this->fail($message);
            } else {
                $this->fail('Exception of type ' . $expected . ' not thrown');
            }
        } catch (\Exception $actual) {
            if (! is_a($actual, $expected)) {
                throw $actual;
            }
        }
    }
}
