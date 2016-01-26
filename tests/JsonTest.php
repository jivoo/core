<?php
namespace Jivoo;

class JsonTest extends \Jivoo\TestCase
{
    public function testEncodeAndDecode()
    {
        $value = 1;
        $this->assertEquals($value, Json::decode(Json::encode($value)));
        
        $value = 'foo';
        $this->assertEquals($value, Json::decode(Json::encode($value)));

        $value = ['foo' => 'bar'];
        $this->assertEquals($value, Json::decode(Json::encode($value)));

        $value = ['foo', 'bar'];
        $this->assertEquals($value, Json::decode(Json::encode($value)));
    }
    
    public function testError()
    {
        $this->assertThrows('Jivoo\JsonException', function () {
            Json::decodeFile('tests/_data/I18n/da.po');
        });
    }
}
