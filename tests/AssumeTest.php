<?php

namespace Jivoo;

class AssumeTest extends \Jivoo\TestCase
{

    public function testThat()
    {
        Assume::that(true);
        $this->assertThrows('Jivoo\InvalidArgumentException', function () {
            Assume::that(false);
        });
        $this->assertThrows('Jivoo\InvalidArgumentException', function () {
            Assume::that(false, 'nothing');
        });
    }
    
    public function testIsSubclassOf()
    {
        Assume::isSubclassOf('Jivoo\InvalidArgumentException', 'Exception');
        Assume::isSubclassOf('Exception', 'Exception');
        $this->assertThrows('Jivoo\InvalidArgumentException', function () {
            Assume::isSubclassOf('Jivoo\DoesNotExist', 'Exception');
        });
        $this->assertThrows('Jivoo\InvalidArgumentException', function () {
            Assume::isSubclassOf('Jivoo\Assume', 'Exception');
        });
        $this->assertThrows('Jivoo\InvalidArgumentException', function () {
            Assume::isSubclassOf(new \Exception(), 'Jivoo\InvalidArgumentException');
        });
    }
    
    public function testIsInstanceOf()
    {
        Assume::isInstanceOf(new \Exception(), 'Exception');
        $this->assertThrows('Jivoo\InvalidArgumentException', function () {
            Assume::isInstanceOf(new \Exception(), 'Jivoo\InvalidArgumentException');
        });
    }
    
    public function testIsType()
    {
        $positives = array(
            'String' => array('foo'),
            'Int' => array(5),
            'Float' => array(5.3),
            'Resource' => array(fopen('tests/_data/I18n/da.mo', 'r')),
            'Object' => array(new \Exception()),
            'Array' => array(array()),
            'Bool' => array(true, false)
        );
        $negatives = array(
            'String' => array(5),
            'Int' => array(5.4, '5'),
            'Float' => array(5, '5.4'),
            'Resource' => array(5),
            'Object' => array(5, fopen('tests/_data/I18n/da.mo', 'r')),
            'Array' => array('foo'),
            'Bool' => array(1, 0, 'true')
        );
        foreach ($positives as $type => $samples) {
            $method = 'is' . $type;
            foreach ($samples as $sample) {
                Assume::$method($sample);
            }
        }
        foreach ($negatives as $type => $samples) {
            $method = 'is' . $type;
            foreach ($samples as $sample) {
                $this->assertThrows('Jivoo\InvalidArgumentException', function () use ($method, $sample) {
                    Assume::$method($sample);
                }, 'Assume::' . $method . ' did not throw an exception for ' . var_export($sample, true));
            }
        }
    }

    public function testIsEmpty()
    {
        Assume::isNonEmpty(array(1));
        $this->assertThrows('Jivoo\InvalidArgumentException', function () {
            Assume::isNonEmpty(array());
        });
    }

    public function testHasKey()
    {
        Assume::hasKey(array('a' => 5), 'a');
        $this->assertThrows('Jivoo\InvalidArgumentException', function () {
            Assume::hasKey(array('a' => 5), 'b');
        });
    }
}
