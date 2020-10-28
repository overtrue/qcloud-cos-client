<?php

namespace Overtrue\CosClient\Tests\Support;

use Overtrue\CosClient\Support\XML;
use Overtrue\CosClient\Tests\TestCase;

class XMLTest extends TestCase
{
    public function testToArray()
    {
        $this->assertSame([
            'DomainConfiguration' =>
                [
                    'DomainRule' =>
                        [
                            [
                                'Status' => 'ENABLED',
                                'Name' => 'cos.cloud.tencent.com',
                                'Type' => 'REST',
                            ],
                            [
                                'Status' => 'ENABLED',
                                'Name' => 'www.cos.cloud.tencent.com',
                                'Type' => 'WEBSITE',
                            ],
                        ],
                    'OptionalFields' => [
                        'Field' => [
                            'Size',
                            'LastModifiedDate',
                            'ETag',
                            'StorageClass',
                            'IsMultipartUploaded',
                            'ReplicationStatus',
                        ],
                    ],
                ],
        ], XML::toArray('<DomainConfiguration>
                                <DomainRule>
                                    <Status>ENABLED</Status>
                                    <Name>cos.cloud.tencent.com</Name>
                                    <Type>REST</Type>
                                </DomainRule>
                                 <OptionalFields>
                                    <Field>Size</Field>
                                    <Field>LastModifiedDate</Field>
                                    <Field>ETag</Field>
                                    <Field>StorageClass</Field>
                                    <Field>IsMultipartUploaded</Field>
                                    <Field>ReplicationStatus</Field>
                                </OptionalFields>
                                <DomainRule>
                                    <Status>ENABLED</Status>
                                    <Name>www.cos.cloud.tencent.com</Name>
                                    <Type>WEBSITE</Type>
                                </DomainRule>
                            </DomainConfiguration>'));
    }

    public function testFromArray()
    {
        $this->assertSame(
            XML::removeSpace('<?xml version="1.0" encoding="utf-8"?>
   <DomainConfiguration>
       <DomainRule>
       <Status>ENABLED</Status>
       <Name>cos.cloud.tencent.com</Name>
       <Type>REST</Type>
       </DomainRule>
       <DomainRule>
       <Status>ENABLED</Status>
       <Name>www.cos.cloud.tencent.com</Name>
       <Type>WEBSITE</Type>
       </DomainRule>
       <OptionalFields>
       <Field>Size</Field>
       <Field>LastModifiedDate</Field>
       <Field>ETag</Field>
       <Field>StorageClass</Field>
       <Field>IsMultipartUploaded</Field>
       <Field>ReplicationStatus</Field>
       </OptionalFields>
       </DomainConfiguration>'),
            XML::removeSpace(XML::fromArray([
                'DomainConfiguration' =>
                    [
                        'DomainRule' => [
                            [
                                'Status' => 'ENABLED',
                                'Name' => 'cos.cloud.tencent.com',
                                'Type' => 'REST',
                            ],
                            [
                                'Status' => 'ENABLED',
                                'Name' => 'www.cos.cloud.tencent.com',
                                'Type' => 'WEBSITE',
                            ],
                        ],
                        'OptionalFields' => [
                            'Field' => [
                                'Size',
                                'LastModifiedDate',
                                'ETag',
                                'StorageClass',
                                'IsMultipartUploaded',
                                'ReplicationStatus',
                            ],
                        ],
                    ],
            ]))
        );
    }
}
