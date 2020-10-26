<?php

namespace Overtrue\CosClient\Tests;

use Overtrue\CosClient\Client;
use Overtrue\CosClient\Config;
use Overtrue\CosClient\Middleware\TransformResponseToArray;
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

        $this->assertCount(2, $client->getMiddlewares());
        $this->assertInstanceOf(CreateRequestSignature::class, $client->getMiddlewares()[0]);
        $this->assertInstanceOf(TransformResponseToArray::class, $client->getMiddlewares()[1]);
    }

    public function testGetConfig()
    {
        $config = new Config([]);
        $client = new Client($config);

        $this->assertSame($config, $client->getConfig());
    }

    public function testGetHttpClient()
    {
        $client = new Client(new Config([]));

        $httpClient = $client->getHttpClient();

        $this->assertInstanceOf(\GuzzleHttp\Client::class, $httpClient);
        $this->assertSame($httpClient, $client->getHttpClient());
    }

    public function testConfigureUserAgent()
    {
        $client = new Client(new Config([]));
        $this->assertSame(
            'overtrue/qcloud-cos-client:'.\GuzzleHttp\Client::MAJOR_VERSION,
            $client->getHttpClientOptions()['headers']['User-Agent']
        );

        $client = new Client(new Config([
            'guzzle' => [
                'headers' => [
                    'User-Agent' => 'custom-user-agent',
                ],
            ],
        ]));
        $this->assertSame('custom-user-agent', $client->getHttpClientOptions()['headers']['User-Agent']);
    }
}
