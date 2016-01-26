<?php
namespace Jivoo;

class EventSubjectBaseTest extends \Jivoo\TestCase
{
    public static function setUpBeforeClass()
    {
        if (! class_exists('Jivoo\TestSubject')) {
            eval(
                'namespace Jivoo;
                class TestSubject extends \Jivoo\EventSubjectBase {
                    protected $events = array("someEvent");
                }'
            );
        }
    }

    public function testAttachAndDetach()
    {
        $subject1 = new TestSubject();
        $c = function () {
            return false;
        };
        $subject1->attachEventHandler('someEvent', $c);
        $this->assertFalse($subject1->triggerEvent('someEvent'));
        $subject1->detachEventHandler('someEvent', $c);
        $this->assertTrue($subject1->triggerEvent('someEvent'));
        $subject1->on('someEvent', $c);
        $this->assertFalse($subject1->triggerEvent('someEvent'));
        $this->assertFalse($subject1->triggerEvent('someEvent'));
        $subject1->detachEventHandler('someEvent', $c);
        $this->assertTrue($subject1->triggerEvent('someEvent'));
        $subject1->one('someEvent', $c);
        $this->assertFalse($subject1->triggerEvent('someEvent'));
        $this->assertTrue($subject1->triggerEvent('someEvent'));
        
        $this->assertThrows('Jivoo\InvalidEventException', function () use ($subject1) {
            $subject1->attachEventHandler('someOtherEvent', function () {
            });
        });
        $this->assertThrows('Jivoo\InvalidEventException', function () use ($subject1) {
            $subject1->triggerEvent('someOtherEvent');
        });
    }

    public function testListener()
    {
        $subject1 = new TestSubject();
        $l = $this->getMockBuilder('Jivoo\EventListener')
            ->setMethods(array(
                'getEventHandlers',
                'someEvent'
            ))
            ->getMock();
        $l->method('getEventHandlers')->wilLReturn(array(
            'someEvent'
        ));
        $l->expects($this->once())
            ->method('someEvent')
            ->willReturn(false);
        $subject1->attachEventListener($l);
        $this->assertFalse($subject1->triggerEvent('someEvent'));
        $subject1->detachEventListener($l);
        $this->assertTrue($subject1->triggerEvent('someEvent'));
    }
}
