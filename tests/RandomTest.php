<?php

namespace Jivoo;

class RandomTest extends \Jivoo\TestCase
{

    public function testLength()
    {
        $this->assertEquals(10, strlen(Random::bytes(10)));
    }

    public function testType()
    {
        $this->assertTrue(is_int(Random::int(0, 10)));
    }
}
