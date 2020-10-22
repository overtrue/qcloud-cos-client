<?php

namespace Overtrue\CosClient;

class Service
{
    protected Client $client;

    /**
     * @param  Client  $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param  string|null  $region
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listBuckets(?string $region = null)
    {
        $uri = $region ? \sprintf('cos.%s.myqcloud.com', $region) : 'service.cos.myqcloud.com';

        return $this->client->get($uri);
    }
}
