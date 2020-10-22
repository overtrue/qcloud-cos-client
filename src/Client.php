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

    protected int $appId;

    protected string $secretId;

    protected string $secretKey;

    protected ?string $signatureExpires = null;

    protected \GuzzleHttp\Client $client;

    protected string $userAgent = 'overtrue/cos-client:'.\GuzzleHttp\Client::MAJOR_VERSION;

    /**
     * @param  int  $appId
     * @param  string  $secretId
     * @param  string  $secretKey
     * @param  array  $httpClientOptions
     */
    public function __construct(int $appId, string $secretId, string $secretKey, array $httpClientOptions = [])
    {
        $this->appId = $appId;
        $this->secretId = $secretId;
        $this->secretKey = $secretKey;

        $this->setHttpClientOptions($httpClientOptions);

        $this->pushMiddleware($this->getSignatureMiddleware());
    }

    public function getClient(): \GuzzleHttp\Client
    {
        return $this->client ?? $this->client = $this->createHttpClient($this->httpClientOptions);
    }

    public function getAppId(): int
    {
        return $this->appId;
    }

    public function getSecretId(): string
    {
        return $this->secretId;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getSignatureMiddleware()
    {
        return Middleware::mapRequest(function (RequestInterface $request) {
            $signature = new Signature($this->secretId, $this->secretKey);

            return $request->withHeader('Authorization', $signature->createAuthorizationHeader($request, $this->signatureExpires));
        });
    }

    public function __call($method, $arguments)
    {
        return \call_user_func_array([$this->getClient(), $method], $arguments);
    }
}
