<?php

namespace Overtrue\CosClient;

use Overtrue\CosClient\Exceptions\InvalidArgumentException;
use Overtrue\CosClient\Exceptions\InvalidConfigException;
use Overtrue\CosClient\Support\XML;

class ObjectClient extends Client
{
    /**
     * @param  \Overtrue\CosClient\Config|array  $config
     *
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function __construct($config)
    {
        if (!($config instanceof Config)) {
            $config = new Config($config);
        }

        if (!$config->has('bucket')) {
            throw new InvalidConfigException('No bucket configured.');
        }

        parent::__construct($config->extend([
            'guzzle' => [
                'base_uri' => \sprintf(
                    'https://%s-%s.cos.%s.myqcloud.com/',
                    $config->get('bucket'),
                    $config->get('app_id'),
                    $config->get('region', self::DEFAULT_REGION)
                ),
            ],
        ]));
    }

    /**
     * @param  string  $key
     * @param  string  $body
     * @param  array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putObject(string $key, string $body, array $headers = [])
    {
        return $this->put($key, \compact('body', 'headers'));
    }

    /**
     * @param  string  $key
     * @param  array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Overtrue\CosClient\Exceptions\InvalidArgumentException
     */
    public function copyObject(string $key, array $headers)
    {
        if (empty($headers['x-cos-copy-source'])) {
            throw new InvalidArgumentException('Missing required header: x-cos-copy-source');
        }

        return $this->put($key, \compact('headers'));
    }

    /**
     * @see https://docs.guzzlephp.org/en/stable/request-options.html#multipart
     *
     * @param  array  $multipart
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Overtrue\CosClient\Exceptions\InvalidArgumentException
     */
    public function postObject(array $multipart)
    {
        if (empty($multipart['key'])) {
            throw new InvalidArgumentException('Missing required keys: key');
        }

        if (empty($multipart['file'])) {
            throw new InvalidArgumentException('Missing required keys: file');
        }

        return $this->post('/', \compact('multipart'));
    }

    /**
     * @param  string  $key
     * @param  array  $query
     * @param  array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getObject(string $key, array $query = [], array $headers = [])
    {
        return $this->get($key, \compact('query', 'headers'));
    }

    /**
     * @param  string  $key
     * @param  string  $versionId
     * @param  array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function headObject(string $key, string $versionId, array $headers = [])
    {
        return $this->head($key, [
            'query' => \compact('versionId'),
            'headers' => $headers,
        ]);
    }

    /**
     * @param  string  $key
     * @param  string  $versionId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteObject(string $key, string $versionId)
    {
        return $this->delete($key, [
            'query' => \compact('versionId'),
        ]);
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteObjects(array $body)
    {
        return $this->post('/?delete', ['body' => XML::fromArray($body)]);
    }

    /**
     * @param  string  $key
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function optionsObject(string $key)
    {
        return $this->options($key);
    }

    /**
     * @param  string  $key
     * @param  string  $versionId
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function restoreObject(string $key, string $versionId, array $body)
    {
        return $this->post($key, [
            'query' => [
                'restore' => '',
                'versionId' => $versionId,
            ],
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @param  string  $key
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function selectObjectContents(string $key, array $body)
    {
        return $this->post($key, [
            'query' => [
                'select' => '',
                'select-type' => 2,
            ],
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @param  string  $key
     * @param  array  $body
     * @param  array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putObjectACL(string $key, array $body, array $headers = [])
    {
        return $this->put($key, [
            'query' => [
                'acl' => '',
            ],
            'body' => XML::fromArray($body),
            'headers' => $headers,
        ]);
    }

    /**
     * @param  string  $key
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getObjectACL(string $key)
    {
        return $this->get($key, [
            'query' => [
                'acl' => '',
            ],
        ]);
    }

    /**
     * @param  string  $key
     * @param  string  $versionId
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putObjectTagging(string $key, string $versionId, array $body)
    {
        return $this->put($key, [
            'query' => [
                'tagging' => '',
                'VersionId' => $versionId,
            ],
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @param  string  $key
     * @param  string  $versionId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getObjectTagging(string $key, string $versionId)
    {
        return $this->get($key, [
            'query' => [
                'tagging' => '',
                'VersionId' => $versionId,
            ],
        ]);
    }

    /**
     * @param  string  $key
     * @param  string  $versionId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteObjectTagging(string $key, string $versionId)
    {
        return $this->delete($key, [
            'query' => [
                'tagging' => '',
                'VersionId' => $versionId,
            ],
        ]);
    }

    /**
     * @param  string  $key
     * @param  array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createUploadId(string $key, array $headers = [])
    {
        return $this->post($key, [
            'query' => [
                'uploads' => '',
            ],
            'headers' => $headers,
        ]);
    }

    /**
     * @param  string  $key
     * @param  int  $partNumber
     * @param  string  $uploadId
     * @param  string  $body
     * @param  array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function uploadPart(string $key, int $partNumber, string $uploadId, string $body, array $headers = [])
    {
        return $this->post($key, [
            'query' => \compact('partNumber', 'uploadId'),
            'headers' => $headers,
            'body' => $body,
        ]);
    }

    /**
     * @param  string  $key
     * @param  int  $partNumber
     * @param  string  $uploadId
     * @param  array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Overtrue\CosClient\Exceptions\InvalidArgumentException
     */
    public function copyPart(string $key, int $partNumber, string $uploadId, array $headers = [])
    {
        if (empty($headers['x-cos-copy-source'])) {
            throw new InvalidArgumentException('Missing required header: x-cos-copy-source');
        }

        return $this->put($key, [
            'query' => \compact('partNumber', 'uploadId'),
            'headers' => $headers,
        ]);
    }

    /**
     * @param  string  $key
     * @param  string  $uploadId
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function markUploadAsCompleted(string $key, string $uploadId, array $body)
    {
        return $this->post($key, [
            'query' => [
                'uploadId' => $uploadId,
            ],
            'body' => $body,
        ]);
    }

    /**
     * @param  string  $key
     * @param  string  $uploadId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function markUploadAsAborted(string $key, string $uploadId)
    {
        return $this->delete($key, [
            'query' => [
                'uploadId' => $uploadId,
            ],
        ]);
    }

    /**
     * @param  array  $query
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getUploadJobs(array $query = [])
    {
        return $this->get('/?uploads', \compact('query'));
    }

    /**
     * @param  string  $key
     * @param  string  $uploadId
     * @param  array  $query
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getUploadedParts(string $key, string $uploadId, array $query = [])
    {
        $query['uploadId'] = $uploadId;

        return $this->get($key, $query);
    }
}
