<?php

namespace Overtrue\CosClient;

use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;

class Client
{
    use CreatesHttpClient;

    protected string $secretId;
    protected string $secretKey;

    protected ?string $signatureExpires = null;
    protected \GuzzleHttp\Client $client;

    protected string $userAgent = 'overtrue/cos-client:'.\GuzzleHttp\Client::MAJOR_VERSION;

    /**
     * @param  string  $secretId
     * @param  string  $secretKey
     */
    public function __construct(string $secretId, string $secretKey)
    {
        $this->secretId = $secretId;
        $this->secretKey = $secretKey;

        $this->pushMiddleware($this->getSignatureMiddleware());
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getClient(): \GuzzleHttp\Client
    {
        return $this->client ?? $this->client = $this->createHttpClient();
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
