<?php

namespace Overtrue\CosClient\Tests;

use Overtrue\CosClient\Exceptions\InvalidArgumentException;
use Overtrue\CosClient\Http\Response;
use Overtrue\CosClient\ObjectClient;
use Overtrue\CosClient\Support\XML;
use PHPUnit\Framework\TestCase;

class ObjectClientTest extends TestCase
{
    public function testBaseUri()
    {
        $client = new ObjectClient([
            'bucket' => 'test',
            'app_id' => '123456',
        ]);

        $this->assertSame('https://test-123456.cos.ap-guangzhou.myqcloud.com/', $client->getBaseUri());
    }

    public function testPutObject()
    {
        $object = ObjectClient::partialMock();

        $body = 'object contents';
        $object->shouldReceive('put')
            ->with('example-key', ['body' => $body, 'headers' => []])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->putObject('example-key', $body);

        $this->assertEmpty($response->toArray());
    }

    public function testCopyObject()
    {
        $object = ObjectClient::partialMock();

        $source = 'sourcebucket-1250000001.cos.ap-shanghai.myqcloud.com/example-%E8%85%BE%E8%AE%AF%E4%BA%91.jpg';
        $object->shouldReceive('put')
            ->with('example-key', ['headers' => ['Content-Type' => 'image/jpeg', 'x-cos-copy-source' => $source]])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->copyObject('example-key', ['Content-Type' => 'image/jpeg', 'x-cos-copy-source' => $source]);
        $this->assertEmpty($response->toArray());

        // invalid arguments
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required header: x-cos-copy-source');
        $object->copyObject('example-key', []);
    }

    public function testPostObject()
    {
        $object = ObjectClient::partialMock();

        $form = [
            [
                'name' => 'key',
                'contents' => 'composer.json',
            ],
            [
                'name' => 'file',
                'filename' => 'composer.json',
                'contents' => fopen(__DIR__.'/../composer.json', 'r+'),
            ],
        ];
        $object->shouldReceive('post')
            ->with('/', ['multipart' => $form])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->postObject($form);

        $this->assertEmpty($response->toArray());
    }

    public function testGetObject()
    {
        $object = ObjectClient::partialMock();
        $object->shouldReceive('get')
            ->with('example-key', ['query' => ['versionId' => 'example-version-id'], 'headers' => ['Range' => 'bytes=5-9']])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->getObject('example-key', ['versionId' => 'example-version-id'], ['Range' => 'bytes=5-9']);

        $this->assertEmpty($response->toArray());
    }

    public function testHeadObject()
    {
        $object = ObjectClient::partialMock();
        $object->shouldReceive('head')
            ->with(
                'example-key',
                ['query' => ['versionId' => 'example-version-id'], 'headers' => ['If-None-Match' => 'ee8de918d05640145b18f70f4c3aa602']]
            )
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->headObject('example-key', 'example-version-id', ['If-None-Match' => 'ee8de918d05640145b18f70f4c3aa602']);

        $this->assertEmpty($response->toArray());
    }

    public function testDeleteObject()
    {
        $object = ObjectClient::partialMock();
        $object->shouldReceive('delete')
            ->with('example-key', ['query' => ['versionId' => 'example-version-id']])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->deleteObject('example-key', 'example-version-id');

        $this->assertEmpty($response->toArray());
    }

    public function testDeleteObjects()
    {
        $object = ObjectClient::partialMock();
        $body = [
            'Delete' => [
                'Quiet' => true,
                'Object' => [
                    'Key' => 'example-key',
                ],
            ],
        ];
        $object->shouldReceive('post')
            ->with('/?delete', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->deleteObjects($body);

        $this->assertEmpty($response->toArray());
    }

    public function testRestoreObject()
    {
        $object = ObjectClient::partialMock();
        $body = [
            'RestoreRequest' => [
                'Days' => 1,
                'CASJobParameters' => [
                    'Tier' => 'Expedited',
                ],
            ],
        ];
        $object->shouldReceive('post')
            ->with('example-key', ['query' => ['restore' => '', 'versionId' => 'example-version-id'], 'body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->restoreObject('example-key', $body, 'example-version-id');

        $this->assertEmpty($response->toArray());
    }

    public function testSelectObjectContents()
    {
        $object = ObjectClient::partialMock();
        $body = [
            'SelectRequest' => [
                'Expression' => 'Select * from COSObject',
                'ExpressionType' => 'SQL',
                'InputSerialization' => [
                    'CompressionType' => 'GZIP',
                    'JSON' =>
                        [
                            'Type' => 'DOCUMENT',
                        ],
                ],
                'OutputSerialization' =>
                    [
                        'JSON' =>
                            [
                                'RecordDelimiter' => '\\n',
                            ],
                    ],
                'RequestProgress' =>
                    [
                        'Enabled' => 'FALSE',
                    ],
            ],
        ];
        $object->shouldReceive('post')
            ->with('example-key', ['query' => ['select' => '', 'select-type' => 2], 'body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->selectObjectContents('example-key', $body);

        $this->assertEmpty($response->toArray());
    }

    public function testPutObjectACL()
    {
        $object = ObjectClient::partialMock();
        $body = [
            'AccessControlPolicy' => [
                'Owner' => [
                    'ID' => 'qcs::cam::uin/100000000001:uin/100000000001',
                ],
                'AccessControlList' =>
                    [
                        'Grant' => [
                            [
                                'Grantee' =>
                                    [
                                        'URI' => 'http://cam.qcloud.com/groups/global/AllUsers',
                                        '@attributes' =>
                                            [
                                                'type' => 'Group',
                                            ],
                                    ],
                                'Permission' => 'READ',
                            ],
                            [
                                'Grantee' =>
                                    [
                                        'ID' => 'qcs::cam::uin/100000000002:uin/100000000002',
                                        '@attributes' =>
                                            [
                                                'type' => 'CanonicalUser',
                                            ],
                                    ],
                                'Permission' => 'READ_ACP',
                            ],
                        ],
                    ],
            ],
        ];

        $object->shouldReceive('put')
            ->with('example-key', ['query' => ['acl' => ''], 'body' => XML::fromArray($body), 'headers' => []])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->putObjectACL('example-key', $body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetObjectAcl()
    {
        $object = ObjectClient::partialMock();
        $object->shouldReceive('get')
            ->with(
                'example-key',
                ['query' => ['acl' => '']]
            )
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->getObjectACL('example-key');

        $this->assertEmpty($response->toArray());
    }

    public function testPutObjectTagging()
    {
        $object = ObjectClient::partialMock();
        $body = [
            'Tagging' => [
                'TagSet' => [
                    'Tag' => ['Key' => 'age', 'Value' => 18],
                ],
            ],
        ];

        $object->shouldReceive('put')
            ->with('example-key', ['query' => ['tagging' => '', 'VersionId' => 'example-version-id'], 'body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->putObjectTagging('example-key', $body, 'example-version-id');

        $this->assertEmpty($response->toArray());
    }

    public function testGetObjectTagging()
    {
        $object = ObjectClient::partialMock();
        $object->shouldReceive('get')
            ->with('example-key', ['query' => ['tagging' => '', 'VersionId' => 'example-version-id']])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->getObjectTagging('example-key', 'example-version-id');

        $this->assertEmpty($response->toArray());
    }

    public function testDeleteObjectTagging()
    {
        $object = ObjectClient::partialMock();
        $object->shouldReceive('delete')
            ->with('example-key', ['query' => ['tagging' => '', 'VersionId' => 'example-version-id']])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->deleteObjectTagging('example-key', 'example-version-id');

        $this->assertEmpty($response->toArray());
    }

    public function testCreateUploadId()
    {
        $object = ObjectClient::partialMock();

        $object->shouldReceive('post')
            ->with('example-key', [
                'query' => ['uploads' => ''], 'headers' => [
                    'Content-Type' => 'image/jpeg',
                ],
            ])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->createUploadId('example-key', [
            'Content-Type' => 'image/jpeg',
        ]);

        $this->assertEmpty($response->toArray());


        // missing content-type
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required headers: Content-Type');
        $object->createUploadId('example-key', [
            'x-cos-grant-read' => 'id="100000000001",id="100000000002"',
        ]);
    }

    public function testPutPart()
    {
        $object = ObjectClient::partialMock();

        $body = 'object contents';
        $object->shouldReceive('put')
            ->with('example-key', [
                'query' => ['partNumber' => 1, 'uploadId' => '1585130821cbb7df1d1xxx'],
                'body' => $body,
                'headers' => [],
            ])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->putPart('example-key', 1, '1585130821cbb7df1d1xxx', $body);

        $this->assertEmpty($response->toArray());
    }

    public function testCopyPart()
    {
        $object = ObjectClient::partialMock();

        $source = 'sourcebucket-1250000001.cos.ap-shanghai.myqcloud.com/example-%E8%85%BE%E8%AE%AF%E4%BA%91.jpg';
        $object->shouldReceive('put')
            ->with('example-key', [
                'query' => ['partNumber' => 1, 'uploadId' => '1585130821cbb7df1d1xxx'],
                'headers' => ['x-cos-copy-source' => $source],
            ])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->copyPart('example-key', 1, '1585130821cbb7df1d1xxx', ['x-cos-copy-source' => $source]);
        $this->assertEmpty($response->toArray());

        // invalid arguments
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required header: x-cos-copy-source');
        $object->copyPart('example-key', 1, '1585130821cbb7df1d1xxx', []);
    }

    public function testMarkUploadAsCompleted()
    {
        $object = ObjectClient::partialMock();
        $body = [
            'CompleteMultipartUpload' => [
                'Part' => [
                    [
                        'PartNumber' => 'integer',
                        'ETag' => 'string',
                    ],
                    [
                        'PartNumber' => 'integer',
                        'ETag' => 'string',
                    ],
                ],
            ],
        ];
        $object->shouldReceive('post')
            ->with('example-key', [
                'query' => ['uploadId' => '1585130821cbb7df1d1xxx'],
                'body' => XML::fromArray($body),
            ])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->markUploadAsCompleted('example-key', '1585130821cbb7df1d1xxx', $body);
        $this->assertEmpty($response->toArray());
    }

    public function testMarkUploadAsAborted()
    {
        $object = ObjectClient::partialMock();

        $object->shouldReceive('delete')
            ->with('example-key', [
                'query' => ['uploadId' => '1585130821cbb7df1d1xxx'],
            ])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->markUploadAsAborted('example-key', '1585130821cbb7df1d1xxx');
        $this->assertEmpty($response->toArray());
    }

    public function testGetUploadJobs()
    {
        $object = ObjectClient::partialMock();

        $object->shouldReceive('get')
            ->with('/?uploads', [
                'query' => ['prefix' => '/images'],
            ])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->getUploadJobs(['prefix' => '/images']);
        $this->assertEmpty($response->toArray());
    }

    public function testGetUploadedParts()
    {
        $object = ObjectClient::partialMock();

        $object->shouldReceive('get')
            ->with('example-key', [
                'query' => ['uploadId' => '1585130821cbb7df1d1xxx'],
            ])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->getUploadedParts('example-key', '1585130821cbb7df1d1xxx');
        $this->assertEmpty($response->toArray());
    }
}
