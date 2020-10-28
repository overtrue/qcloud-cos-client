<?php

namespace Overtrue\CosClient\Tests;

use Overtrue\CosClient\BucketClient;
use Overtrue\CosClient\Http\Response;
use Overtrue\CosClient\Support\XML;

class BucketClientTest extends TestCase
{
    public function testPutBucket()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'CreateBucketConfiguration' => [
                'BucketAZConfig' => 'MAZ',
            ],
        ];
        $bucket->shouldReceive('put')
            ->with('/', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putBucket($body);

        $this->assertEmpty($response->toArray());
    }

    public function testDeleteBucket()
    {
        $bucket = BucketClient::partialMock();

        $bucket->shouldReceive('delete')
            ->with('/')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->deleteBucket();

        $this->assertEmpty($response->toArray());
    }

    public function testHeadBucket()
    {
        $bucket = BucketClient::partialMock();

        $bucket->shouldReceive('head')
            ->with('/')
            ->andReturn(Response::create(200, ['x-cos-bucket-az-type' => 'MAZ']));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->headBucket();

        $this->assertEmpty($response->toArray());
        $this->assertArrayHasKey('x-cos-bucket-az-type', $response->getHeaders());
    }

    public function testGetObjects()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/', ['query' => ['prefix' => 'images']])
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<ListBucketResult>
                        <Name>demo</Name>
                        <Prefix>images</Prefix>
                    </ListBucketResult>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getObjects(['prefix' => 'images']);

        $this->assertArrayHasKey('ListBucketResult', $response->toArray());
        $this->assertSame('demo', $response->toArray()['ListBucketResult']['Name']);
    }

    public function testGetObjectVersions()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?versions', ['query' => ['prefix' => 'images']])
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<ListVersionsResult>
                        <Name>demo</Name>
                        <Prefix>images</Prefix>
                    </ListVersionsResult>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getObjectVersions(['prefix' => 'images']);

        $this->assertArrayHasKey('ListVersionsResult', $response->toArray());
        $this->assertSame('demo', $response->toArray()['ListVersionsResult']['Name']);
    }

    public function testPutAcl()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'AccessControlPolicy' => [
                'Owner' => ['ID' => 'qcs::cam::uin/100000000001:uin/100000000001'],
                'AccessControlList' => [
                    'Grant' => [
                        'Grantee' => ['URI' => 'http://cam.qcloud.com/groups/global/AllUsers'],
                        'Permission' => 'READ',
                    ],
                ],
            ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?acl', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putACL($body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetAcl()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?acl')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<AccessControlPolicy>
                        <Owner>
                            <ID>qcs::cam::uin/100000000001:uin/100000000001</ID>
                            <DisplayName>qcs::cam::uin/100000000001:uin/100000000001</DisplayName>
                        </Owner>
                        <AccessControlList>
                            <Grant>
                                <Grantee xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="Group">
                                    <URI>http://cam.qcloud.com/groups/global/AllUsers</URI>
                                </Grantee>
                                <Permission>READ</Permission>
                            </Grant>
                        </AccessControlList>
                    </AccessControlPolicy>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getACL();

        $this->assertArrayHasKey('AccessControlPolicy', $response->toArray());
        $this->assertSame('qcs::cam::uin/100000000001:uin/100000000001', $response->toArray()['AccessControlPolicy']['Owner']['ID']);
    }

    public function testPutCors()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'CORSConfiguration' => [
                'CORSRule' => [
                    'AllowedOrigin' => '*',
                    'AllowedMethod' => ['GET', 'HEAD'],
                    'AllowedHeader' => 'Range',
                    'MaxAgeSeconds' => 600,
                ],
            ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?cors', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putCORS($body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetCors()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?cors')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<CORSConfiguration>
                        <CORSRule>
                            <AllowedOrigin>*</AllowedOrigin>
                            <AllowedMethod>GET</AllowedMethod>
                            <AllowedMethod>HEAD</AllowedMethod>
                            <AllowedHeader>Range</AllowedHeader>
                            <AllowedHeader>x-cos-server-side-encryption-customer-algorithm</AllowedHeader>
                            <AllowedHeader>x-cos-server-side-encryption-customer-key</AllowedHeader>
                            <AllowedHeader>x-cos-server-side-encryption-customer-key-MD5</AllowedHeader>
                            <ExposeHeader>Content-Length</ExposeHeader>
                            <ExposeHeader>ETag</ExposeHeader>
                            <ExposeHeader>x-cos-meta-author</ExposeHeader>
                            <MaxAgeSeconds>600</MaxAgeSeconds>
                        </CORSRule>
                      </CORSConfiguration>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getCORS();

        $this->assertArrayHasKey('CORSConfiguration', $response->toArray());
        $this->assertSame([
            'GET', 'HEAD',
        ], $response->toArray()['CORSConfiguration']['CORSRule']['AllowedMethod']);
    }

    public function testDeleteCors()
    {
        $bucket = BucketClient::partialMock();

        $bucket->shouldReceive('delete')
            ->with('/?cors')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->deleteCORS();

        $this->assertEmpty($response->toArray());
    }

    public function testPutLifecycle()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'LifecycleConfiguration' => [
                'Rule' => [
                    'ID' => 'id1',
                    'Filter' => ['Prefix' => 'documents/'],
                    'Status' => 'Enabled',
                    'Transition' => [
                        'Days' => 100,
                        'StorageClass' => 'ARCHIVE',
                    ],
                ],
            ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?lifecycle', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putLifecycle($body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetLifecycle()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?lifecycle')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<LifecycleConfiguration>
                          <Rule>
                            <ID>id1</ID>
                            <Filter>
                               <Prefix>documents/</Prefix>
                            </Filter>
                            <Status>Enabled</Status>
                            <Transition>
                              <Days>100</Days>
                              <StorageClass>STANDARD_IA</StorageClass>
                            </Transition>
                          </Rule>
                        </LifecycleConfiguration>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getLifecycle();

        $this->assertArrayHasKey('LifecycleConfiguration', $response->toArray());
        $this->assertSame('documents/', $response->toArray()['LifecycleConfiguration']['Rule']['Filter']['Prefix']);
    }

    public function testDeleteLifecycle()
    {
        $bucket = BucketClient::partialMock();

        $bucket->shouldReceive('delete')
            ->with('/?lifecycle')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->deleteLifecycle();

        $this->assertEmpty($response->toArray());
    }

    public function testPutPolicy()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'Statement' => [
                'Principal' => [
                    'qcs' => [
                        "qcs::cam::uin/100000000001:uin/100000000011",
                    ],
                    "Effect" => "allow",
                    'Action' => [
                        "name/cos:GetBucket",
                    ],
                    "Resource" => [
                        "qcs::cos:ap-guangzhou:uid/1250000000:examplebucket-1250000000/*",
                    ],
                ],
            ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?policy', ['json' => $body])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putPolicy($body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetPolicy()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?policy')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/json'],
                '{
                          "Statement": [
                            {
                              "Principal": {
                                "qcs": [
                                  "qcs::cam::uin/100000000001:uin/100000000001"
                                ]
                              },
                              "Effect": "allow",
                              "Action": [
                                "name/cos:GetBucket"
                              ],
                              "Resource": [
                                "qcs::cos:ap-guangzhou:uid/1250000000:examplebucket-1250000000/*"
                              ]
                            }
                          ],
                          "version": "2.0"
                        }'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getPolicy();

        $this->assertArrayHasKey('Statement', $response->toArray());
        $this->assertSame([
            'Principal' => [
                'qcs' => [
                    "qcs::cam::uin/100000000001:uin/100000000001",
                ],
            ],
            "Effect" => "allow",
            'Action' => [
                "name/cos:GetBucket",
            ],
            "Resource" => [
                "qcs::cos:ap-guangzhou:uid/1250000000:examplebucket-1250000000/*",
            ],
        ], $response->toArray()['Statement'][0]);
    }

    public function testDeletePolicy()
    {
        $bucket = BucketClient::partialMock();

        $bucket->shouldReceive('delete')
            ->with('/?policy')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->deletePolicy();

        $this->assertEmpty($response->toArray());
    }

    public function testPutReferer()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'RefererConfiguration' => [
                'Status' => 'Enabled',
                'RefererType' => 'White-List',
                'DomainList' => ['Domain' => ['*.qq.com', '*.qcloud.com'],],
                'EmptyReferConfiguration' => 'allow',
            ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?referer', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putReferer($body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetReferer()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?referer')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<RefererConfiguration>
                    <Status>Enabled</Status>
                    <RefererType>White-List</RefererType>
                    <DomainList>
                        <Domain>*.qq.com</Domain>
                        <Domain>*.qcloud.com</Domain>
                    </DomainList>
                    <EmptyReferConfiguration>Allow</EmptyReferConfiguration>
                </RefererConfiguration>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getReferer();

        $this->assertArrayHasKey('RefererConfiguration', $response->toArray());
        $this->assertSame('Enabled', $response->toArray()['RefererConfiguration']['Status']);
    }

    public function testPutTagging()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'Tagging' => [
                'TagSet' => [
                    'Tag' => [
                        'Key' => 'age',
                        'Value' => 18,
                    ],
                ],
            ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?tagging', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putTagging($body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetTagging()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?tagging')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<Tagging>
                    <TagSet>
                        <Tag>
                            <Key>age</Key>
                            <Value>18</Value>
                        </Tag>
                        <Tag>
                            <Key>name</Key>
                            <Value>xiaoming</Value>
                        </Tag>
                    </TagSet>
                </Tagging>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getTagging();

        $this->assertArrayHasKey('Tagging', $response->toArray());
        $this->assertSame('age', $response->toArray()['Tagging']['TagSet']['Tag'][0]['Key']);
    }

    public function testDeleteTagging()
    {
        $bucket = BucketClient::partialMock();

        $bucket->shouldReceive('delete')
            ->with('/?tagging')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->deleteTagging();

        $this->assertEmpty($response->toArray());
    }

    public function testPutWebsite()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'WebsiteConfiguration' => [
                'IndexDocument' => [
                    'Suffix' => 'string',
                ],
                'RedirectAllRequestsTo' => [
                    'Protocol' => 'string',
                ],
                'ErrorDocument' => [
                    'Key' => 'string',
                ],
                'RoutingRules' => [
                    'RoutingRule' => [
                        [
                            'Condition' => [
                                'HttpErrorCodeReturnedEquals' => 'integer',
                            ],
                            'Redirect' => [
                                'Protocol' => 'string',
                                'ReplaceKeyWith' => 'string',
                            ],
                        ],
                        [
                            'Condition' => [
                                'KeyPrefixEquals' => 'string',
                            ],
                            'Redirect' => [
                                'Protocol' => 'string',
                                'ReplaceKeyPrefixWith' => 'string',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?website', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putWebsite($body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetWebsite()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?website')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<WebsiteConfiguration>
                    <IndexDocument>
                        <Suffix>index.html</Suffix>
                    </IndexDocument>
                    <RedirectAllRequestsTo>
                        <Protocol>https</Protocol>
                    </RedirectAllRequestsTo>
                    <ErrorDocument>
                        <Key>pages/error.html</Key>
                    </ErrorDocument>
                    <RoutingRules>
                        <RoutingRule>
                            <Condition>
                                <HttpErrorCodeReturnedEquals>403</HttpErrorCodeReturnedEquals>
                            </Condition>
                            <Redirect>
                                <Protocol>https</Protocol>
                                <ReplaceKeyWith>pages/403.html</ReplaceKeyWith>
                            </Redirect>
                        </RoutingRule>
                        <RoutingRule>
                            <Condition>
                                <HttpErrorCodeReturnedEquals>404</HttpErrorCodeReturnedEquals>
                            </Condition>
                            <Redirect>
                                <ReplaceKeyWith>pages/404.html</ReplaceKeyWith>
                            </Redirect>
                        </RoutingRule>
                        <RoutingRule>
                            <Condition>
                                <KeyPrefixEquals>assets/</KeyPrefixEquals>
                            </Condition>
                            <Redirect>
                                <ReplaceKeyWith>index.html</ReplaceKeyWith>
                            </Redirect>
                        </RoutingRule>
                        <RoutingRule>
                            <Condition>
                                <KeyPrefixEquals>article/</KeyPrefixEquals>
                            </Condition>
                            <Redirect>
                                <Protocol>https</Protocol>
                                <ReplaceKeyPrefixWith>archived/</ReplaceKeyPrefixWith>
                            </Redirect>
                        </RoutingRule>
                    </RoutingRules>
                </WebsiteConfiguration>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getWebsite();

        $this->assertArrayHasKey('WebsiteConfiguration', $response->toArray());
        $this->assertSame('index.html', $response->toArray()['WebsiteConfiguration']['IndexDocument']['Suffix']);
    }

    public function testDeleteWebsite()
    {
        $bucket = BucketClient::partialMock();

        $bucket->shouldReceive('delete')
            ->with('/?website')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->deleteWebsite();

        $this->assertEmpty($response->toArray());
    }

    public function testPutInventory()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'InventoryConfiguration' =>
                [
                    'Id' => 'list1',
                    'IsEnabled' => 'true',
                    'Destination' =>
                        [
                            'COSBucketDestination' =>
                                [
                                    'Format' => 'CSV',
                                    'AccountId' => '100000000001',
                                    'Bucket' => 'qcs::cos:ap-guangzhou::examplebucket-1250000000',
                                    'Prefix' => 'list1',
                                    'Encryption' => ['SSE-COS' => '',],
                                ],
                        ],
                    'Schedule' => ['Frequency' => 'Daily',],
                    'Filter' => ['Prefix' => 'myPrefix',],
                    'IncludedObjectVersions' => 'All',
                    'OptionalFields' => [
                        'Field' => [
                            'Size', 'LastModifiedDate', 'ETag', 'StorageClass', 'IsMultipartUploaded', 'ReplicationStatus',
                        ],
                    ],
                ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?inventory&id=inventory-configuration-ID', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putInventory('inventory-configuration-ID', $body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetInventory()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?inventory&id=demo-id')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<InventoryConfiguration xmlns = "http://....">
                    <Id>list1</Id>
                    <IsEnabled>true</IsEnabled>
                    <Destination>
                        <COSBucketDestination>
                            <Format>CSV</Format>
                            <AccountId>1250000000</AccountId>
                            <Bucket>qcs::cos:ap-guangzhou::examplebucket-1250000000</Bucket>
                            <Prefix>list1</Prefix>
                            <Encryption>
                                <SSE-COS></SSE-COS>
                            </Encryption>
                        </COSBucketDestination>
                    </Destination>
                    <Schedule>
                        <Frequency>Daily</Frequency>
                    </Schedule>
                    <Filter>
                        <Prefix>myPrefix</Prefix>
                    </Filter>
                    <IncludedObjectVersions>All</IncludedObjectVersions>
                    <OptionalFields>
                        <Field>Size</Field>
                        <Field>LastModifiedDate</Field>
                        <Field>ETag</Field>
                        <Field>StorageClass</Field>
                        <Field>IsMultipartUploaded</Field>
                        <Field>ReplicationStatus</Field>
                    </OptionalFields>
                </InventoryConfiguration>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getInventory('demo-id');

        $this->assertArrayHasKey('InventoryConfiguration', $response->toArray());
        $this->assertSame('list1', $response->toArray()['InventoryConfiguration']['Id']);
    }

    public function testDeleteInventory()
    {
        $bucket = BucketClient::partialMock();

        $bucket->shouldReceive('delete')
            ->with('/?inventory&id=demo-id')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->deleteInventory('demo-id');

        $this->assertEmpty($response->toArray());
    }

    public function testPutVersioning()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'VersioningConfiguration' =>
                [
                    'Status' => 'Enabled',
                ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?versioning', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putVersioning($body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetVersioning()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?versioning')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<VersioningConfiguration>
                  <Status>Suspended</Status>
                </VersioningConfiguration>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getVersioning();

        $this->assertArrayHasKey('VersioningConfiguration', $response->toArray());
        $this->assertSame('Suspended', $response->toArray()['VersioningConfiguration']['Status']);
    }

    public function testPutReplication()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'ReplicationConfiguration' =>
                [
                    'Status' => 'Enabled',
                ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?replication', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putReplication($body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetReplication()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?replication')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<ReplicationConfiguration>
                  <Status>Suspended</Status>
                </ReplicationConfiguration>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getReplication();

        $this->assertArrayHasKey('ReplicationConfiguration', $response->toArray());
        $this->assertSame('Suspended', $response->toArray()['ReplicationConfiguration']['Status']);
    }

    public function testDeleteReplication()
    {
        $bucket = BucketClient::partialMock();

        $bucket->shouldReceive('delete')
            ->with('/?replication')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->deleteReplication();

        $this->assertEmpty($response->toArray());
    }

    public function testPutLogging()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'BucketLoggingStatus' =>
                [
                    'LoggingEnabled' =>
                        [
                            'TargetBucket' => 'examplebucket-1250000000',
                            'TargetPrefix' => 'prefix',
                        ],
                ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?logging', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putLogging($body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetLogging()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?logging')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<BucketLoggingStatus>
                  <LoggingEnabled>
                    <TargetBucket>examplebucket-1250000000</TargetBucket>
                    <TargetPrefix>prefix</TargetPrefix>
                  </LoggingEnabled>
                </BucketLoggingStatus>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getLogging();

        $this->assertArrayHasKey('BucketLoggingStatus', $response->toArray());
        $this->assertSame('examplebucket-1250000000', $response->toArray()['BucketLoggingStatus']['LoggingEnabled']['TargetBucket']);
    }

    public function testPutAccelerate()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'AccelerateConfiguration' =>
                [
                    'Status' => 'Enabled',
                ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?accelerate', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putAccelerate($body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetAccelerate()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?accelerate')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<AccelerateConfiguration>
                  <Status>Disabled</Status>
                  <Type>COS</Type>
                </AccelerateConfiguration>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getAccelerate();

        $this->assertArrayHasKey('AccelerateConfiguration', $response->toArray());
        $this->assertSame('Disabled', $response->toArray()['AccelerateConfiguration']['Status']);
    }

    public function testPutEncryption()
    {
        $bucket = BucketClient::partialMock();

        $body = [
            'ServerSideEncryptionConfiguration' =>
                [
                    'Rule' =>
                        [
                            'ApplySideEncryptionConfiguration' =>
                                [
                                    'SSEAlgorithm' => 'AES256',
                                ],
                        ],
                ],
        ];
        $bucket->shouldReceive('put')
            ->with('/?encryption', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->putEncryption($body);

        $this->assertEmpty($response->toArray());
    }

    public function testGetEncryption()
    {
        $bucket = BucketClient::partialMock();
        $bucket->shouldReceive('get')
            ->with('/?encryption')
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<ServerSideEncryptionConfiguration>
                   <Rule>
                      <ApplySideEncryptionConfiguration>
                         <SSEAlgorithm>AES256</SSEAlgorithm>
                      </ApplySideEncryptionConfiguration>
                   </Rule>
                </ServerSideEncryptionConfiguration>'
            ));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->getEncryption();

        $this->assertArrayHasKey('ServerSideEncryptionConfiguration', $response->toArray());
        $this->assertSame(
            'AES256',
            $response->toArray()['ServerSideEncryptionConfiguration']['Rule']['ApplySideEncryptionConfiguration']['SSEAlgorithm']
        );
    }

    public function testDeleteEncryption()
    {
        $bucket = BucketClient::partialMock();

        $bucket->shouldReceive('delete')
            ->with('/?encryption')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var BucketClient $bucket */
        $response = $bucket->deleteEncryption();

        $this->assertEmpty($response->toArray());
    }
}
