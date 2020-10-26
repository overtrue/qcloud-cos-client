<?php

namespace Overtrue\CosClient\Tests;

use Overtrue\CosClient\Service;

class ServiceTest extends TestCase
{
    public function testListBuckets()
    {
        $service = Service::spy();

        $service->shouldReceive('get')->with('https://service.cos.myqcloud.com')->once()->andReturn('all region buckets');

        $this->assertSame('all region buckets', $service->listBuckets());


        $service->shouldReceive('get')->with('https://cos.ap-guangzhou.myqcloud.com')->once()->andReturn('all guangzhou buckets');

        $this->assertSame('all guangzhou buckets', $service->listBuckets('ap-guangzhou'));
    }
}
