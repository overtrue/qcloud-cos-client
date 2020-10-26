<?php

namespace Overtrue\CosClient;

use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

/**
 * @method \Psr\Http\Message\ResponseInterface get($uri, array $options = [])
 * @method \Psr\Http\Message\ResponseInterface head($uri, array $options = [])
 * @method \Psr\Http\Message\ResponseInterface put($uri, array $options = [])
 * @method \Psr\Http\Message\ResponseInterface post($uri, array $options = [])
 * @method \Psr\Http\Message\ResponseInterface patch($uri, array $options = [])
 * @method \Psr\Http\Message\ResponseInterface delete($uri, array $options = [])
 * @method \Psr\Http\Message\ResponseInterface request(string $method, $uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface getAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface headAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface putAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface postAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface patchAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface deleteAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface requestAsync(string $method, $uri, array $options = [])
 */
class Client
{
    use CreatesHttpClient;

    protected Config $config;

    protected \GuzzleHttp\Client $client;

    protected string $userAgent = 'overtrue/cos-client:'.\GuzzleHttp\Client::MAJOR_VERSION;

    /**
     * @param  \Overtrue\CosClient\Config  $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->setHttpClientOptions($config->get('guzzle'));

        $this->pushMiddleware($this->getSignatureMiddleware());
    }

    public function getClient(): \GuzzleHttp\Client
    {
        return $this->client ?? $this->client = $this->createHttpClient();
    }

    public function getAppId(): int
    {
        return $this->config->get('app_id');
    }

    public function getSecretId(): string
    {
        return $this->config->get('secret_id');
    }

    public function getSecretKey(): string
    {
        return $this->config->get('secret');
    }

    public function getSignatureMiddleware()
    {
        return Middleware::mapRequest(function (RequestInterface $request) {
            return $request->withHeader(
                'Authorization',
                (new Signature($this->getSecretId(), $this->getSecretKey()))
                    ->createAuthorizationHeader($request, $this->get('signature_expires'))
            );
        });
    }

    public function __call($method, $arguments)
    {
        return \call_user_func_array([$this->getClient(), $method], $arguments);
    }
}
