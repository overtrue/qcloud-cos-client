<?php

namespace Overtrue\CosClient\Tests;

use Overtrue\CosClient\Client;
use Overtrue\CosClient\Config;
use Overtrue\CosClient\Middleware\CreateRequestSignature;
use Overtrue\CosClient\Middleware\SetContentMd5;

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
        $this->assertInstanceOf(SetContentMd5::class, $client->getMiddlewares()[1]);
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

    public function testConfigureDomain()
    {
        $client = new Client(new Config([
            'app_id' => 10020201024,
            'bucket' => 'example',
            'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx',
            'secret_key' => 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx',
        ]));

        $httpClient = $client->getHttpClient();

        $this->assertInstanceOf(\GuzzleHttp\Client::class, $httpClient);
        $this->assertSame('https://example-10020201024.cos.ap-guangzhou.myqcloud.com/', $client->getBaseUri());

        $client = new Client(new Config([
            'app_id' => 10020201024,
            'bucket' => 'example',
            'domain' => '',
            'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx',
            'secret_key' => 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx',
        ]));

        $httpClient = $client->getHttpClient();

        $this->assertInstanceOf(\GuzzleHttp\Client::class, $httpClient);
        $this->assertSame('https://example-10020201024.cos.ap-guangzhou.myqcloud.com/', $client->getBaseUri());

        $client = new Client(new Config([
            'app_id' => 10020201024,
            'bucket' => 'example',
            'domain' => 'example.com',
            'secret_id' => 'AKIDsiQzQla780mQxLLU2GJCxxxxxxxxxxx',
            'secret_key' => 'b0GMH2c2NXWKxPhy77xhHgwxxxxxxxxxxx',
        ]));

        $httpClient = $client->getHttpClient();

        $this->assertInstanceOf(\GuzzleHttp\Client::class, $httpClient);
        $this->assertSame('https://example.com/', $client->getBaseUri());
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
}
