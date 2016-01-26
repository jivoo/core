<?php
namespace Jivoo\Cache;

class NullPoolTest extends \Jivoo\TestCase
{

    public function testAlwaysEmpty()
    {
        $pool = new NullPool();
        $pool->set('foo', 'bar');
        $this->assertFalse($pool->hasItem('foo'));
        $this->assertTrue($pool->clear());
        $this->assertTrue($pool->deleteItem('foo'));
        $this->assertSame($pool, $pool->deleteItems(array('foo', 'bar')));
        
        $item = $pool->getItem('foo');
        $this->assertEquals('foo', $item->getKey());
        $this->assertNull($item->get());
        $this->assertSame($item, $item->expiresAfter(100));
        $pool->saveDeferred($item);
        $this->assertTrue($pool->commit());
        $this->assertFalse($pool->hasItem('foo'));
    }
}
