<?php

namespace Overtrue\CosClient\Tests;

use Overtrue\CosClient\Http\Response;
use Overtrue\CosClient\JobClient;
use Overtrue\CosClient\Support\XML;

class JobClientTest extends TestCase
{
    public function testCustomDomain()
    {
        $object = JobClient::partialMockWithConfig([
            'uin' => '12345600',
            'app_id' => '12345600',
            'region' => 'ap-guangzhou',
            'secret_id' => 'mock-secret_id',
            'secret_key' => 'mock-secret_key',
            'bucket' => 'example-12345600',
        ]);

        $this->assertSame('https://12345600.cos-control.ap-guangzhou.myqcloud.com/', $object->getConfig()['guzzle']['base_uri']);

        $object = JobClient::partialMockWithConfig([
            'uin' => '12345600',
            'app_id' => '12345600',
            'region' => 'ap-guangzhou',
            'secret_id' => 'mock-secret_id',
            'secret_key' => 'mock-secret_key',
            'bucket' => 'example-12345600',
            'use_https' => false,
            'domain' => 'example-12345600.abc.cos-control.test.com',
        ]);

        $this->assertSame('http://example-12345600.abc.cos-control.test.com/', $object->getConfig()['guzzle']['base_uri']);

        $object = JobClient::partialMockWithConfig([
            'uin' => '12345600',
            'app_id' => '12345600',
            'region' => 'ap-guangzhou',
            'secret_id' => 'mock-secret_id',
            'secret_key' => 'mock-secret_key',
            'bucket' => 'example-12345600',
            'use_https' => true,
            'domain' => 'example-12345600.abc.cos-control.test.com',
        ]);

        $this->assertSame('https://example-12345600.abc.cos-control.test.com/', $object->getConfig()['guzzle']['base_uri']);
    }

    public function testListJobs()
    {
        $job = JobClient::partialMock();

        $job->shouldReceive('get')
            ->with('/jobs', [
                'query' => [
                    'jobStatuses' => 'Active',
                    'maxResults' => 1,
                    'nextToken' => '086711ca-15da-4e89-7676-03f1a1346623',
                ]
            ])
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<ListJobsResult>
                        <Jobs>
                            <member>
                                <CreationTime>2020-10-27T11:07:05Z</CreationTime>
                                <Description>example-job</Description>
                                <JobId>021140d8-67ca-4e89-8089-0de9a1e40943</JobId>
                                <Operation>COSPutObjectCopy</Operation>
                                <Priority>10</Priority>
                                <ProgressSummary>
                                    <NumberOfTasksFailed>0</NumberOfTasksFailed>
                                    <NumberOfTasksSucceeded>10</NumberOfTasksSucceeded>
                                    <TotalNumberOfTasks>10</TotalNumberOfTasks>
                                </ProgressSummary>
                                <Status>Complete</Status>
                                <TerminationDate>2020-10-27T11:07:21Z</TerminationDate>
                            </member>
                            <member>
                                <CreationTime>2020-10-27T11:07:05Z</CreationTime>
                                <Description>example-job</Description>
                                <JobId>066d919e-49b9-429e-b844-e17ea7b16421</JobId>
                                <Operation>COSPutObjectCopy</Operation>
                                <Priority>10</Priority>
                                <ProgressSummary>
                                    <NumberOfTasksFailed>0</NumberOfTasksFailed>
                                    <NumberOfTasksSucceeded>10</NumberOfTasksSucceeded>
                                    <TotalNumberOfTasks>10</TotalNumberOfTasks>
                                </ProgressSummary>
                                <Status>Complete</Status>
                                <TerminationDate>2020-10-27T11:07:21Z</TerminationDate>
                            </member>
                        </Jobs>
                        <NextToken>066d919e-49b9-429e-b844-e17ea7b16421</NextToken>
                    </ListJobsResult>'
            ));

        /* @var Response $response */
        /* @var JobClient $job */
        $response = $job->getJobs([
            'jobStatuses' => 'Active',
            'maxResults' => 1,
            'nextToken' => '086711ca-15da-4e89-7676-03f1a1346623',
        ]);

        $this->assertArrayHasKey('ListJobsResult', $response->toArray());
        $this->assertSame('066d919e-49b9-429e-b844-e17ea7b16421', $response->toArray()['ListJobsResult']['NextToken']);
    }

    public function testCreateJob()
    {
        $job = JobClient::partialMock();

        $body = [
            'CreateJobRequest' => [
                'ClientRequestToken' => 'string',
                'ConfirmationRequired' => 'boolean',
                'Description' => 'string',
                'Manifest' => [
                    'Location' => [
                        'ETag' => 'string',
                        'ObjectArn' => 'string',
                        'ObjectVersionId' => 'string',
                    ],
                    'Spec' => [
                        'Fields' => [
                            'member' => [
                                'string',
                                'string'
                            ]
                        ],
                        'Format' => 'string'
                    ]
                ],
            ],
            'Operation' => [
                'COSInitiateRestoreObject' => [],
                'COSPutObjectCopy' => [],
            ],
            'Priority' => 'integer',
            'Report' => [
                'Bucket' => 'string',
            ],
            'RoleArn' => 'string'
        ];

        $job->shouldReceive('post')
            ->with('/jobs', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<CreateJobResult>
                       <JobId>086711ca-15da-4e89-7676-03f1a1346623</JobId>
                    </CreateJobResult>'
            ));

        /* @var Response $response */
        /* @var JobClient $job */
        $response = $job->createJob($body);

        $this->assertSame('086711ca-15da-4e89-7676-03f1a1346623', $response->toArray()['CreateJobResult']['JobId']);
    }

    public function testDescribeJob()
    {
        $job = JobClient::partialMock();

        $job->shouldReceive('get')
            ->with(\sprintf('/jobs/%s', '086711ca-15da-4e89-7676-03f1a1346623'))
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<DescribeJobResult>
                        <Job>
                            <ConfirmationRequired>false</ConfirmationRequired>
                            <CreationTime>2020-10-27T18:00:30Z</CreationTime>
                            <Description>example-job</Description>
                            <FailureReasons>
                                <JobFailure>
                                    <FailureCode/>
                                    <FailureReason/>
                                </JobFailure>
                            </FailureReasons>
                            <JobId>086711ca-15da-4e89-7676-03f1a1346623</JobId>
                            <Manifest>
                                <Location>
                                    <ETag>&quot;15150651828fa9cdcb8356b6d1c7638b&quot;</ETag>
                                    <ObjectArn>qcs::cos:ap-guangzhou::sourcebucket-1250000000/manifests/batch-copy-manifest.csv</ObjectArn>
                                </Location>
                                <Spec>
                                    <Fields>
                                        <member>Bucket</member>
                                        <member>Key</member>
                                    </Fields>
                                    <Format>COSBatchOperations_CSV_V1</Format>
                                </Spec>
                            </Manifest>
                        </Job>
                    </DescribeJobResult>'
            ));

        /* @var Response $response */
        /* @var JobClient $job */
        $response = $job->describeJob('086711ca-15da-4e89-7676-03f1a1346623');

        $this->assertArrayHasKey('DescribeJobResult', $response->toArray());
        $this->assertSame('086711ca-15da-4e89-7676-03f1a1346623', $response->toArray()['DescribeJobResult']['Job']['JobId']);
    }

    public function testUpdateJobPriority()
    {
        $job = JobClient::partialMock();

        $job->shouldReceive('post')
            ->with(\sprintf('/jobs/%s/priority', '086711ca-15da-4e89-7676-03f1a1346623'), [
                'query' => [
                    'priority' => 1
                ]
            ])
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<UpdateJobPriorityResult>
                        <JobId>086711ca-15da-4e89-7676-03f1a1346623</JobId>
                        <Priority>1</Priority>
                    </UpdateJobPriorityResult>'
            ));

        /* @var Response $response */
        /* @var JobClient $job */
        $response = $job->updateJobPriority('086711ca-15da-4e89-7676-03f1a1346623', 1);

        $this->assertSame(1, \intval($response->toArray()['UpdateJobPriorityResult']['Priority']));
    }

    public function UpdateJobStatus()
    {
        $job = JobClient::partialMock();

        $job->shouldReceive('post')
            ->with(\sprintf('/jobs/%s/status', '086711ca-15da-4e89-7676-03f1a1346623'), [
                'query' => [
                    'requestedJobStatus' => 'Cancelled',
                    'statusUpdateReason' => '取消操作',
                ]
            ])
            ->andReturn(Response::create(
                200,
                ['Content-Type' => 'application/xml'],
                '<UpdateJobStatusResult>
                        <JobId>086711ca-15da-4e89-7676-03f1a1346623</JobId>
                        <Status>Cancelled</Status>
                        <StatusUpdateReason>取消操作</StatusUpdateReason>
                    </UpdateJobStatusResult>'
            ));

        /* @var Response $response */
        /* @var JobClient $job */
        $response = $job->updateJobStatus('086711ca-15da-4e89-7676-03f1a1346623', [
            'requestedJobStatus' => 'Cancelled',
            'statusUpdateReason' => '取消操作',
        ]);

        $this->assertSame('Cancelled', \intval($response->toArray()['UpdateJobStatusResult']['Status']));
    }
}
