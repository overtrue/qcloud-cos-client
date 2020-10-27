<?php

namespace Overtrue\CosClient;

use Overtrue\CosClient\Exceptions\InvalidConfigException;
use Overtrue\CosClient\Support\XML;

class BucketClient extends Client
{
    public const DEFAULT_REGION = 'ap-guangzhou';

    /**
     * @param  \Overtrue\CosClient\Config  $config
     *
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    public function __construct(Config $config)
    {
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
            ]
        ]));
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create(array $body)
    {
        return $this->put('/', [
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function ping()
    {
        return $this->head('/');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function destroy()
    {
        return $this->delete('/');
    }

    /**
     * @param  array  $query
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getObjects(array $query = [])
    {
        return $this->get('/', \compact('query'));
    }

    /**
     * @param  array  $query
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getObjectVersions(array $query = [])
    {
        return $this->get('/?versions', \compact('query'));
    }

    /**
     * @param  array  $body
     * @param  array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putACL(array $body, array $headers = [])
    {
        return $this->put('/?acl', \array_filter([
            'headers' => $headers,
            'body' => XML::fromArray($body),
        ]));
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getACL()
    {
        return $this->get('/?acl');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putCORS(array $body)
    {
        return $this->put('/?cors', [
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getCORS()
    {
        return $this->get('/?cors');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteCORS()
    {
        return $this->delete('/?cors');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putLifecycle(array $body)
    {
        return $this->put('/?lifecycle', [
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getLifecycle()
    {
        return $this->get('/?lifecycle');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteLifecycle()
    {
        return $this->delete('/?lifecycle');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putPolicy(array $body)
    {
        return $this->put('/?policy', ['json' => $body]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getPolicy()
    {
        return $this->get('/?policy');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deletePolicy()
    {
        return $this->delete('/?policy');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putReferer(array $body)
    {
        return $this->put('/?referer', [
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getReferer()
    {
        return $this->get('/?referer');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putTagging(array $body)
    {
        return $this->put('/?tagging', [
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getTagging()
    {
        return $this->get('/?tagging');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteTagging()
    {
        return $this->delete('/?tagging');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putWebsite(array $body)
    {
        return $this->put('/?website', [
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getWebsite()
    {
        return $this->get('/?website');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteWebsite()
    {
        return $this->delete('/?website');
    }

    /**
     * @param  string  $id
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putInventory(string $id, array $body)
    {
        return $this->put(\sprintf('/?inventory&id=%s', $id), [
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @param  string  $id
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getInventory(string $id)
    {
        return $this->get(\sprintf('/?inventory&id=%s', $id));
    }

    /**
     * @param  string|null  $nextContinuationToken
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listInventoryConfigurations(?string $nextContinuationToken = null)
    {
        return $this->get(\sprintf('/?inventory&continuation-token=%s', $nextContinuationToken));
    }

    /**
     * @param  string  $id
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteInventory(string $id)
    {
        return $this->delete(\sprintf('/?inventory&id=%s', $id));
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putVersioning(array $body)
    {
        return $this->put('/?versioning', [
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getVersioning()
    {
        return $this->get('/?versioning');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putReplication(array $body)
    {
        return $this->put('/?replication', [
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getReplication()
    {
        return $this->get('/?replication');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteReplication()
    {
        return $this->delete('/?replication');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putLogging(array $body)
    {
        return $this->put('/?logging', [
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getLogging()
    {
        return $this->get('/?logging');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putAccelerate(array $body)
    {
        return $this->put('/?accelerate', [
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getAccelerate()
    {
        return $this->get('/?accelerate');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putEncryption(array $body)
    {
        return $this->put('/?encryption', [
            'body' => XML::fromArray($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getEncryption()
    {
        return $this->get('/?encryption');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteEncryption()
    {
        return $this->delete('/?encryption');
    }
}
