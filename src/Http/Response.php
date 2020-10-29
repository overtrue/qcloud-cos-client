<?php

namespace Overtrue\CosClient\Http;

use Overtrue\CosClient\Support\XML;
use Psr\Http\Message\ResponseInterface;

class Response extends \GuzzleHttp\Psr7\Response implements \JsonSerializable, \ArrayAccess
{
    protected ?array $arrayResult = null;

    public function __construct(ResponseInterface $response)
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

        $contents = $this->getContents();

        if (empty($contents)) {
            return $this->arrayResult = null;
        }

        return $this->arrayResult = $this->isXML() ? XML::toArray($contents) : \json_decode($contents, true);
    }

    public function toObject()
    {
        return \json_decode(\json_encode($this->toArray()));
    }

    public function isXML()
    {
        return \strpos($this->getHeaderLine('content-type'), 'xml') > 0;
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

    public static function create(
        $status = 200,
        array $headers = [],
        $body = null,
        $version = '1.1',
        $reason = null
    ) {
        return new self(new \GuzzleHttp\Psr7\Response($status, $headers, $body, $version, $reason));
    }

    public function toString()
    {
        return $this->getContents();
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        $this->getBody()->rewind();

        return $this->getBody()->getContents();
    }
}
