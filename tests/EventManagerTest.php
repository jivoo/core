<?php
namespace Jivoo;

class EventManagerTest extends \Jivoo\TestCase
{

    public function testAttachAndDetach()
    {
        $subject1 = $this->getMockBuilder('Jivoo\EventSubject')->getMock();
        $subject1->method('getEvents')->willReturn(array(
            'someEvent'
        ));
        $subject2 = $this->getMockBuilder('Jivoo\EventSubject')->getMock();
        $subject2->method('getEvents')->willReturn(array(
            'someEvent'
        ));
        $em1 = new EventManager($subject1);
        $em2 = new EventManager($subject2, $em1);
        $c = function (Event $event) {
            $event->stopPropagation();
            return false;
        };
        $em1->attachHandler('someEvent', $c);
        $this->assertFalse($em1->trigger('someEvent'));
        $this->assertTrue($em1->detachHandler('someEvent', $c));
        $this->assertFalse($em1->detachHandler('someEvent', $c));
        $this->assertTrue($em1->trigger('someEvent'));
        $em2->attachHandler(get_class($subject1) . '.someEvent', $c);
        $this->assertFalse($em2->trigger(get_class($subject1) . '.someEvent'));
        $this->assertTrue($em2->detachHandler(get_class($subject1) . '.someEvent', $c));
        $this->assertTrue($em2->trigger(get_class($subject1) . '.someEvent'));
        
        $this->assertFalse($em1->detachHandler('someOtherEvent', 'time'));
        
        $this->assertThrows('Jivoo\InvalidEventException', function () use ($em1) {
            $em1->attachHandler('someOtherEvent', function () {
            });
        });
        $this->assertThrows('Jivoo\InvalidEventException', function () use ($em1) {
            $em1->trigger('someOtherEvent');
        });
    }

    public function testListener()
    {
        $subject1 = $this->getMockBuilder('Jivoo\EventSubject')->getMock();
        $subject1->method('getEvents')->willReturn(array(
            'someEvent'
        ));
        $subject2 = $this->getMockBuilder('Jivoo\EventSubject')->getMock();
        $subject2->method('getEvents')->willReturn(array(
            'someEvent'
        ));
        $em1 = new EventManager($subject1);
        $em2 = new EventManager($subject2, $em1);
        $l = $this->getMockBuilder('Jivoo\EventListener')
            ->setMethods(array(
            'getEventHandlers',
            'someEvent'
            ))
            ->getMock();
        $l->method('getEventHandlers')->wilLReturn(array(
            get_class($subject2) . '.someEvent'
        ));
        $l->expects($this->once())
            ->method('someEvent')
            ->willReturn(false);
        $em1->attachListener($l);
        $this->assertFalse($em2->trigger('someEvent'));
        $em1->detachListener($l);
        $this->assertTrue($em2->trigger('someEvent'));
    }
}
