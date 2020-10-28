<?php

namespace Overtrue\CosClient\Tests;

use Overtrue\CosClient\Exceptions\InvalidArgumentException;
use Overtrue\CosClient\Http\Response;
use Overtrue\CosClient\ObjectClient;
use Overtrue\CosClient\Support\XML;
use PHPUnit\Framework\TestCase;

class ObjectClientTest extends TestCase
{
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
            ->with('example-key', ['headers' => ['x-cos-copy-source' => $source]])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->copyObject('example-key', ['x-cos-copy-source' => $source]);
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
            'key' => 'example-file',
            'file' => 'fopen-resource',
        ];
        $object->shouldReceive('post')
            ->with('/', ['multipart' => $form])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->postObject($form);

        $this->assertEmpty($response->toArray());

        // missing key
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required keys: key');
        $object->postObject(['file' => 'fopen-resource']);

        // missing file
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required keys: key');
        $object->postObject(['key' => 'example-file']);
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

    public function testOptionsObject()
    {
        $object = ObjectClient::partialMock();
        $object->shouldReceive('options')
            ->with('example-key')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var ObjectClient $object */
        $response = $object->optionsObject('example-key');

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
        $response = $object->restoreObject('example-key', 'example-version-id', $body);

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
}
