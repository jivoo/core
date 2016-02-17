<?php
namespace Jivoo\Log;

use Jivoo\TestCase;
use Psr\Log\LogLevel;

class LoggerTest extends TestCase
{
    
    public function testLog()
    {
        $log = new Logger();
        
        $log->emergency('foo1');
        $log->alert('foo2');
        $log->critical('foo3');
        $log->error('foo4');
        $log->warning('foo5');
        $log->notice('foo6');
        $log->info('foo7');
        $log->debug('foo8');
        
        $messages = array_map(function ($record) {
            return $record['message'];
        }, $log->getLog());
        
        $this->assertEquals(['foo1', 'foo2', 'foo3', 'foo4', 'foo5', 'foo6', 'foo7', 'foo8'], $messages);
        
        $this->assertThrows('Jivoo\Log\InvalidArgumentException', function () use ($log) {
            $log->log('end_of_the_world', 'foobar');
        });
    }
    
    public function testCompare()
    {
        $this->assertLessThan(0, Logger::compare(LogLevel::ALERT, LogLevel::EMERGENCY));
        $this->assertGreaterThan(0, Logger::compare(LogLevel::ALERT, LogLevel::INFO));
        $this->assertEquals(0, Logger::compare(LogLevel::WARNING, LogLevel::WARNING));
    }
    
    public function testInterpolate()
    {
        $this->assertEquals('foo bar', Logger::interpolate('{a} {b}', ['a' => 'foo', 'b' => 'bar']));
        $this->assertEquals('foo {b}', Logger::interpolate('{a} {b}', ['a' => 'foo']));
    }
    
    public function testAddHandler()
    {
        $handler = $this->getMockForAbstractClass('Jivoo\Log\HandlerBase', [LogLevel::INFO]);
        
        $handler->expects($this->exactly(3))
            ->method('handle');
        
        $log = new Logger();
        $log->emergency('foo1');
        $log->alert('foo2');
        $log->debug('foo3');
        
        $log->addHandler($handler);
                
        $log->info('foo4');
        $log->debug('foo5');
    }
}
