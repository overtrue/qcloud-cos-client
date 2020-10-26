<?php

namespace Overtrue\CosClient;

use Overtrue\CosClient\Support\XML;

class Response extends \GuzzleHttp\Psr7\Response implements \JsonSerializable, \ArrayAccess
{
    protected array $arrayResult;

    public function __construct(Response $response)
    {
        parent::__construct(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    public function toArray()
    {
        if (!\is_null($this->arrayResult)) {
            return $this->arrayResult;
        }

        $this->getBody()->rewind();

        $contents = $this->getBody()->getContents();

        if (empty($contents)) {
            throw new \Exception('Empty Response.');
        }

        return XML::parse($contents);
    }

    public function jsonSerialize()
    {
        try {
            return $this->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function offsetExists($offset)
    {
        return \array_key_exists($offset, $this->toArray());
    }

    public function offsetGet($offset)
    {
        return $this->toArray()[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        return null;
    }

    public function offsetUnset($offset)
    {
        return null;
    }
}
