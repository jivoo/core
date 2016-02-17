<?php
namespace Jivoo\Cache;

class WrapperPooltest extends \Jivoo\TestCase
{
  
    public function testWrapper()
    {
        $pool = $this->getMock('Psr\Cache\CacheItemPoolInterface');
        $wrapper = new WrapperPool($pool);
        
        $pool->expects($this->once())
            ->method('getItem')
            ->with($this->equalTo('foo'))
            ->willReturn(new MutableItem('foo', 'bar', true));
        $this->assertEquals('bar', $wrapper->getItem('foo')->get());
        
        $pool->expects($this->once())
            ->method('getItems')
            ->with($this->equalTo(['foo']))
            ->willReturn([new MutableItem('foo', 'bar', true)]);
        $this->assertEquals('bar', $wrapper->getItems(['foo'])[0]->get());
        
        $pool->expects($this->once())
            ->method('hasItem')
            ->with($this->equalTo('foo'))
            ->willReturn(true);
        $this->assertTrue($wrapper->hasItem('foo'));
        
        $pool->expects($this->once())
            ->method('clear')
            ->willReturn(true);
        $this->assertTrue($wrapper->clear());
        
        $pool->expects($this->once())
            ->method('deleteItem')
            ->with($this->equalTo('foo'))
            ->willReturn(true);
        $this->assertTrue($wrapper->deleteItem('foo'));
        
        $pool->expects($this->once())
            ->method('deleteItems')
            ->with($this->equalTo(['foo']))
            ->willReturn(true);
        $this->assertTrue($wrapper->deleteItems(['foo']));
        
        $item = new NullItem('foo');
        $pool->expects($this->once())
            ->method('save')
            ->with($this->equalTo($item))
            ->willReturn(true);
        $this->assertTrue($wrapper->save($item));
        
        $pool->expects($this->once())
            ->method('saveDeferred')
            ->with($this->equalTo($item))
            ->willReturn(true);
        $this->assertTrue($wrapper->saveDeferred($item));
        
        $pool->expects($this->once())
            ->method('commit')
            ->willReturn(true);
        $this->assertTrue($wrapper->commit());
        
    }
}
