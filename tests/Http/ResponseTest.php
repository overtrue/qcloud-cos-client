<?php

namespace Overtrue\CosClient\Tests\Http;

use Overtrue\CosClient\Http\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testToArray()
    {
        $response = new Response(new \GuzzleHttp\Psr7\Response(200));

        $this->assertSame(null, $response->toArray());

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'text/html'], '<html><body>contents</body></html>'));
        $this->assertSame(null, $response->toArray());

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'application/xml'], '<html><body>contents</body></html>'));
        $this->assertSame([
            'html' => [
                'body' => 'contents'
            ],
        ], $response->toArray());

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'application/xml'], '<foo><bar>value1</bar><bar>value2</bar></foo>'));
        $this->assertSame([
            'foo' => [
                'bar' => ['value1', 'value2'],
            ],
        ], $response->toArray());

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'application/json'], '{"foo":{"bar":["value1","value2"]}}'));
        $this->assertSame([
            'foo' => [
                'bar' => ['value1', 'value2'],
            ],
        ], $response->toArray());
    }

    public function testToObject()
    {
        $response = new Response(new \GuzzleHttp\Psr7\Response(200));

        $this->assertSame(null, $response->toObject());

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'text/html'], '<html><body>contents</body></html>'));
        $this->assertSame(null, $response->toObject());

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'application/xml'], '<html><body>contents</body></html>'));
        $this->assertInstanceOf(\stdClass::class, $response->toObject());
        $this->assertSame('contents', $response->toObject()->html->body);

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'application/xml'], '<foo><bar>value1</bar><bar>value2</bar></foo>'));
        $this->assertInstanceOf(\stdClass::class, $response->toObject());
        $this->assertSame(['value1', 'value2'], $response->toObject()->foo->bar);

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'application/json'], '{"foo":{"bar":["value1","value2"]}}'));
        $this->assertInstanceOf(\stdClass::class, $response->toObject());
        $this->assertSame(['value1', 'value2'], $response->toObject()->foo->bar);
    }

    public function testIsXML()
    {
        $response = new Response(new \GuzzleHttp\Psr7\Response(200));

        $this->assertFalse($response->isXML());

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'text/html'], '<html><body>contents</body></html>'));
        $this->assertFalse($response->isXML());

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'application/xml'], '<html><body>contents</body></html>'));
        $this->assertTrue($response->isXML());

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'application/json'], '{"foo":{"bar":["value1","value2"]}}'));
        $this->assertFalse($response->isXML());
    }

    public function testGetContents()
    {
        $response = new Response(new \GuzzleHttp\Psr7\Response(200));
        $this->assertEmpty($response->getContents());

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'application/json'], '{"foo":{"bar":["value1","value2"]}}'));
        $this->assertSame('{"foo":{"bar":["value1","value2"]}}', $response->getContents());

        $response = new Response(new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'application/xml'], '<html><body>contents</body></html>'));
        $this->assertSame('<html><body>contents</body></html>', $response->getContents());
    }
}
