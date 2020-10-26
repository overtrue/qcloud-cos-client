<?php

namespace Overtrue\CosClient;

class Bucket extends Client
{
    protected Config $config;

    /**
     * @param  \Overtrue\CosClient\Config  $config
     */
    public function __construct(Config $config)
    {
        parent::__construct($config->extend([
            'guzzle' => [
                'base_uri' => \sprintf(
                    'https://%s-%s.cos.%s.myqcloud.com/',
                    $config->get('bucket'),
                    $config->get('app_id'),
                    $config->get('region')
                ),
            ]
        ]));
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function put(array $body)
    {
        return parent::put('/', [
            'body' => XML::build($body),
        ]);
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
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function head()
    {
        return parent::head('/');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete()
    {
        return parent::delete('/');
    }

    /**
     * @param  array  $query
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getObjectVersions(array $query = [])
    {
        return $this->get('?versions', \compact('query'));
    }

    /**
     * @param  array  $body
     * @param  array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putACL(array $body, array $headers = [])
    {
        return parent::put('/?acl', [
            'headers' => $headers,
            'body' => XML::build($body),
        ]);
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
        return parent::put('/?cors', [
            'body' => XML::build($body),
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
        return parent::delete('/?cors');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putLifecycle(array $body)
    {
        return parent::put('/?lifecycle', [
            'body' => XML::build($body),
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
        return parent::delete('/?lifecycle');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putPolicy(array $body)
    {
        return parent::put('/?policy', ['json' => $body]);
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
        return parent::delete('/?policy');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putReferer(array $body)
    {
        return parent::put('/?referer', [
            'body' => XML::build($body),
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
        return parent::put('/?tagging', [
            'body' => XML::build($body),
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
        return parent::delete('/?tagging');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putWebsite(array $body)
    {
        return parent::put('/?website', [
            'body' => XML::build($body),
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
        return parent::delete('/?website');
    }

    /**
     * @param  string  $id
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putInventory(string $id, array $body)
    {
        return parent::put('/?inventory', [
            'query' => \compact('id'),
            'body' => XML::build($body),
        ]);
    }

    /**
     * @param  string  $id
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getInventory(string $id)
    {
        return $this->get('/?inventory', [
            'query' => \compact('id'),
        ]);
    }

    /**
     * @param  string|null  $nextContinuationToken
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listInventoryConfigurations(?string $nextContinuationToken = null)
    {
        return $this->get('/?inventory', [
            'query' => ['continuation-token' => $nextContinuationToken],
        ]);
    }

    /**
     * @param  string  $id
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function deleteInventory(string $id)
    {
        return parent::delete('/?inventory', [
            'query' => \compact('id'),
        ]);
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putVersioning(array $body)
    {
        return parent::put('/?versioning', [
            'body' => XML::build($body),
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
        return parent::put('/?replication', [
            'body' => XML::build($body),
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
        return parent::delete('/?replication');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function putLogging(array $body)
    {
        return parent::put('/?logging', [
            'body' => XML::build($body),
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
        return parent::put('/?accelerate', [
            'body' => XML::build($body),
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
        return parent::put('/?encryption', [
            'body' => XML::build($body),
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
        return parent::delete('/?encryption');
    }
}
