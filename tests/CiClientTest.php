<?php

namespace Overtrue\CosClient\Tests;

use Overtrue\CosClient\CiClient;
use Overtrue\CosClient\Http\Response;
use Overtrue\CosClient\Support\XML;

class CiClientTest extends TestCase
{
    public function testDetectImage()
    {
        $object = CiClient::partialMock();
        $body = [
            'Request' => [
                'Input' => [
                    ['Object' => 'a.jpg'],
                    ['Url' => 'htts://overtrue.me/avatar.jpg'],
                ],
                'Conf' => [
                    'DetectType' => 'Porn,Ads',
                ],
            ],
        ];
        $object->shouldReceive('post')
            ->with('/image/auditing', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->detectImage($body);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetImageJob()
    {
        $object = CiClient::partialMock();
        $object->shouldReceive('get')
            ->with('/image/auditing/mock-id')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->getImageJob('mock-id');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDetectVideo()
    {
        $object = CiClient::partialMock();
        $body = [
            'Request' => [
                'Input' => [
                    ['Object' => 'a.mp4'],
                ],
                'Conf' => [
                    'DetectType' => 'Porn,Ads',
                ],
            ],
        ];
        $object->shouldReceive('post')
            ->with('/video/auditing', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->detectVideo($body);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetVideoJob()
    {
        $object = CiClient::partialMock();
        $object->shouldReceive('get')
            ->with('/video/auditing/mock-id')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->getVideoJob('mock-id');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDetectAudio()
    {
        $object = CiClient::partialMock();
        $body = [
            'Request' => [
                'Input' => [
                    ['Object' => 'a.mp3'],
                ],
                'Conf' => [
                    'DetectType' => 'Porn,Ads',
                ],
            ],
        ];
        $object->shouldReceive('post')
            ->with('/audio/auditing', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->detectAudio($body);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetAudioJob()
    {
        $object = CiClient::partialMock();
        $object->shouldReceive('get')
            ->with('/audio/auditing/mock-id')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->getAudioJob('mock-id');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDetectText()
    {
        $object = CiClient::partialMock();
        $body = [
            'Request' => [
                'Input' => [
                    ['Object' => 'a.txt'],
                ],
                'Conf' => [
                    'DetectType' => 'Porn,Ads',
                ],
            ],
        ];
        $object->shouldReceive('post')
            ->with('/text/auditing', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->detectText($body);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetTextJob()
    {
        $object = CiClient::partialMock();
        $object->shouldReceive('get')
            ->with('/text/auditing/mock-id')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->getTextJob('mock-id');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDetectDocument()
    {
        $object = CiClient::partialMock();
        $body = [
            'Request' => [
                'Input' => [
                    ['Url' => 'http://www.example.com/doctest.doc', 'Dataid' => '123-abcd-efdh'],
                ],
                'Conf' => [
                    'DetectType' => 'Porn,Ads',
                ],
            ],
        ];
        $object->shouldReceive('post')
            ->with('/document/auditing', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->detectDocument($body);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetDocumentJob()
    {
        $object = CiClient::partialMock();
        $object->shouldReceive('get')
            ->with('/document/auditing/mock-id')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->getDocumentJob('mock-id');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDetectWebPage()
    {
        $object = CiClient::partialMock();
        $body = [
            'Request' => [
                'Input' => [
                    ['Url' => 'http://github.com/overtrue/qcloud-cos-client', 'Dataid' => '123-abcd-efdh'],
                ],
                'Conf' => [
                    'DetectType' => 'Porn,Ads',
                ],
            ],
        ];
        $object->shouldReceive('post')
            ->with('/webpage/auditing', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->detectWebPage($body);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetWebPageJob()
    {
        $object = CiClient::partialMock();
        $object->shouldReceive('get')
            ->with('/webpage/auditing/mock-id')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->getWebPageJob('mock-id');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDetectLiveVideo()
    {
        $object = CiClient::partialMock();
        $body = [
            'Request' => [
                'Input' => [
                    ['Url' => 'rtmp://example.com/live/123', 'Dataid' => '123-abcd-efdh'],
                ],
                'Conf' => [
                    'DetectType' => 'Porn,Ads',
                ],
            ],
        ];
        $object->shouldReceive('post')
            ->with('/video/auditing', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->detectLiveVideo($body);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testGetLiveVideoJob()
    {
        $object = CiClient::partialMock();
        $object->shouldReceive('get')
            ->with('/video/auditing/mock-id')
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->getLiveVideoJob('mock-id');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testReportBadcase()
    {
        $object = CiClient::partialMock();
        $body = [
            'Request' => [
                'ContentType' => 1,
                'Url' => 'http://www.example.com/abc.jpg',
                'Label' => 'Porn',
                'SuggestedLabel' => 'Normal',
            ],
        ];
        $object->shouldReceive('post')
            ->with('/report/badcase', ['body' => XML::fromArray($body)])
            ->andReturn(Response::create(200));

        /* @var Response $response */
        /* @var CiClient $object */
        $response = $object->reportBadcase($body);

        $this->assertSame(200, $response->getStatusCode());
    }
}
