<?php

namespace Overtrue\CosClient\Tests;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Overtrue\CosClient\Client;
use Overtrue\CosClient\Config;
use Overtrue\CosClient\Middleware\CreateRequestSignature;

class ClientTest extends TestCase
{
    public function testGetAppId()
    {
        $client = new Client(new Config([
            'app_id' => 10020201024,
            'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx',
            'secret_key' => 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx',
        ]));

        $this->assertSame(10020201024, $client->getAppId());
    }

    public function testGetSecretKey()
    {
        $client = new Client(new Config([
            'app_id' => 10020201024,
            'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx',
            'secret_key' => 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx',
        ]));

        $this->assertSame('b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx', $client->getSecretKey());
    }

    public function testGetSecretId()
    {
        $client = new Client(new Config([
            'app_id' => 10020201024,
            'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx',
            'secret_key' => 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx',
        ]));

        $this->assertSame('AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx', $client->getSecretId());
    }

    public function testGetSignatureMiddleware()
    {
        $client = new Client(new Config([
            'app_id' => 10020201024,
            'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx',
            'secret_key' => 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx',
        ]));

        $this->assertCount(1, $client->getMiddlewares());
        $this->assertInstanceOf(CreateRequestSignature::class, $client->getMiddlewares()[0]);
    }

    public function testGetConfig()
    {
        $config = new Config([
            'app_id' => 10020201024,
            'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx',
            'secret_key' => 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx',
        ]);
        $client = new Client($config);

        $this->assertSame($config, $client->getConfig());
    }

    public function testGetHttpClient()
    {
        $client = new Client(new Config([
            'app_id' => 10020201024,
            'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx',
            'secret_key' => 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx',
        ]));

        $httpClient = $client->getHttpClient();

        $this->assertInstanceOf(\GuzzleHttp\Client::class, $httpClient);
        $this->assertSame($httpClient, $client->getHttpClient());
    }

    public function testConfigureUserAgent()
    {
        $client = new Client(new Config([
            'app_id' => 10020201024,
            'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx',
            'secret_key' => 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx',
        ]));
        $this->assertSame(
            'overtrue/qcloud-cos-client:'.\GuzzleHttp\Client::MAJOR_VERSION,
            $client->getHttpClientOptions()['headers']['User-Agent']
        );

        $client = new Client(new Config([
            'app_id' => 10020201024,
            'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx',
            'secret_key' => 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx',
            'guzzle' => [
                'headers' => [
                    'User-Agent' => 'custom-user-agent',
                ],
            ],
        ]));
        $this->assertSame('custom-user-agent', $client->getHttpClientOptions()['headers']['User-Agent']);
    }

    public function testTransformResponseXMLToArray()
    {
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/xml'],
                '
                <Owner>
                    <ID>string</ID>
                    <DisplayName>string</DisplayName>
                </Owner>'
            ),
            new Response(202, ['Content-Length' => 0]),
            new RequestException('Error Communicating with Server', new Request('GET', '/test')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $httpClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);

        $client = Client::partialMock();
        $client->shouldReceive('getHttpClient')->andReturn($httpClient);
        $client->shouldReceive('get')->passthru();

        $this->assertSame(['Owner' => ['ID' => 'string', 'DisplayName' => 'string']], $client->get('/test')->toArray());
    }
}
