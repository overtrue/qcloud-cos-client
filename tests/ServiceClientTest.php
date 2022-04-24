<?php

namespace Overtrue\CosClient\Tests;

use Overtrue\CosClient\Http\Response;
use Overtrue\CosClient\ServiceClient;

class ServiceClientTest extends TestCase
{
    public function testListBuckets()
    {
        $service = ServiceClient::partialMock();

        $service->shouldReceive('get')
            ->with('https://service.cos.myqcloud.com/')
            ->once()
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<ListAllMyBucketsResult>
                        <Owner>
                            <ID>qcs::cam::uin/100000000001:uin/100000000001</ID>
                            <DisplayName>100000000001</DisplayName>
                        </Owner>
                        <Buckets>
                            <Bucket>
                                <Name>examplebucket1-1250000000</Name>
                                <Location>ap-beijing</Location>
                                <CreationDate>2019-05-24T11:49:50Z</CreationDate>
                            </Bucket>
                        </Buckets>
                    </ListAllMyBucketsResult>'
            ));

        $this->assertSame([
            'ListAllMyBucketsResult' => [
                'Owner' => [
                    'ID' => 'qcs::cam::uin/100000000001:uin/100000000001',
                    'DisplayName' => '100000000001'
                ],
                'Buckets' => [
                    'Bucket' => [
                        'Name' => 'examplebucket1-1250000000',
                        'Location' => 'ap-beijing',
                        'CreationDate' => '2019-05-24T11:49:50Z'
                    ]
                ]
            ]
        ], $service->getBuckets()->toArray());

        $service->shouldReceive('get')
            ->with('https://cos.ap-guangzhou.myqcloud.com/')
            ->once()
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<ListAllMyBucketsResult>
                        <Owner>
                            <ID>qcs::cam::uin/100000000001:uin/100000000001</ID>
                            <DisplayName>100000000001</DisplayName>
                        </Owner>
                        <Buckets>
                            <Bucket>
                                <Name>examplebucket1-1250000000</Name>
                                <Location>ap-beijing</Location>
                                <CreationDate>2019-05-24T11:49:50Z</CreationDate>
                            </Bucket>
                        </Buckets>
                    </ListAllMyBucketsResult>'
            ));

        $this->assertSame([
            'ListAllMyBucketsResult' => [
                'Owner' => [
                    'ID' => 'qcs::cam::uin/100000000001:uin/100000000001',
                    'DisplayName' => '100000000001'
                ],
                'Buckets' => [
                    'Bucket' => [
                        'Name' => 'examplebucket1-1250000000',
                        'Location' => 'ap-beijing',
                        'CreationDate' => '2019-05-24T11:49:50Z'
                    ]
                ]
            ]
        ], $service->getBuckets('ap-guangzhou')->toArray());
    }
}
