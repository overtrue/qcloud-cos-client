<?php

namespace Overtrue\CosClient\Middleware;

use Overtrue\CosClient\XML;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TransformResponseToArray
{
    public function __invoke(callable $handler)
    {
        return static function (RequestInterface $request, array $options) use ($handler) {
            return $handler($request, $options)->then(function (ResponseInterface $response) {
                $response->getBody()->rewind();

                $contents = $response->getBody()->getContents();

                if (empty($contents)) {
                    throw new \Exception('Empty Response.');
                }

                return XML::parse($contents);
            });
        };
    }
}
