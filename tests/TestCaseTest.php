<?php
namespace Jivoo;

class TestCaseTest extends \Jivoo\TestCase
{
    public function testEncodeAndDecode()
    {
        $this->assertThrows('Exception', function () {
            $this->assertThrows('Exception', function () {
                // no exception
            });
        });
        $this->assertThrows('Exception', function () {
            $this->assertThrows('Exception', function () {
                // no exception
            }, 'message');
        });
        $this->assertThrows('InvalidArgumentException', function () {
            $this->assertThrows('DomainException', function () {
                // wrong exception
                throw new \InvalidargumentException('foo');
            }, 'message');
        });
    }
}
