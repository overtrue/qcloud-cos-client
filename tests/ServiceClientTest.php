<?php

namespace Overtrue\CosClient\Tests;

use Overtrue\CosClient\Http\Response;
use Overtrue\CosClient\ServiceClient;

class ServiceClientTest extends TestCase
{
    public function testListBuckets()
    {
        /** @var ServiceClient $service */
        $service = ServiceClient::partialMock();

        $service->shouldReceive('get')
            ->with('https://service.cos.myqcloud.com/')
            ->once()
            ->andReturn(new Response(new \GuzzleHttp\Psr7\Response(200, [], '{"Buckets": [{"Name": "test"}]}')));

        $this->assertSame([
            'Buckets' => [
                [
                    'Name' => 'test',
                ],
            ],
        ], $service->getBuckets()->toArray());

        $service->shouldReceive('get')
            ->with('https://cos.ap-guangzhou.myqcloud.com/')
            ->once()
            ->andReturn(new Response(new \GuzzleHttp\Psr7\Response(200, [], '{"Buckets": [{"Name": "test"}]}')));

        $this->assertSame([
            'Buckets' => [
                [
                    'Name' => 'test',
                ],
            ],
        ], $service->getBuckets('ap-guangzhou')->toArray());
    }
}
