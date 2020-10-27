<?php

namespace Overtrue\CosClient;

class ServiceClient extends Client
{
    /**
     * @param  string|null  $region
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listBuckets(?string $region = null)
    {
        $uri = $region ? \sprintf('https://cos.%s.myqcloud.com', $region) : 'https://service.cos.myqcloud.com';

        return $this->get($uri);
    }
}
