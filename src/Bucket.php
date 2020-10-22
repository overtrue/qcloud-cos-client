<?php

namespace Overtrue\CosClient;

class Bucket
{
    protected string $name;

    protected string $region;

    protected \GuzzleHttp\Client $client;

    /**
     * @param  string  $name
     * @param  string  $region
     * @param  Client  $client
     */
    public function __construct(string $name, string $region, Client $client)
    {
        $this->name = $name;
        $this->region = $region;
        $this->client = $client->createHttpClient([
            'base_uri' => \sprintf('https://%s-%s.cos.%s.myqcloud.com/', $name, $client->getAppId(), $region),
        ]);
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(array $body)
    {
        return $this->client->put('/', [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @param  array  $query
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getObjects(array $query = [])
    {
        return $this->client->get('/', \compact('query'));
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function head()
    {
        return $this->client->head('/');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete()
    {
        return $this->client->delete('/');
    }

    /**
     * @param  array  $query
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getObjectVersions(array $query = [])
    {
        return $this->client->get('?versions', \compact('query'));
    }

    /**
     * @param  array  $body
     * @param  array  $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putACL(array $body, array $headers = [])
    {
        return $this->client->put('/?acl', [
            'headers' => $headers,
            'body' => XML::build($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getACL()
    {
        return $this->client->get('/?acl');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putCORS(array $body)
    {
        return $this->client->put('/?cors', [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCORS()
    {
        return $this->client->get('/?cors');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteCORS()
    {
        return $this->client->delete('/?cors');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putLifecycle(array $body)
    {
        return $this->client->put('/?lifecycle', [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLifecycle()
    {
        return $this->client->get('/?lifecycle');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteLifecycle()
    {
        return $this->client->delete('/?lifecycle');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putPolicy(array $body)
    {
        return $this->client->put('/?policy', ['json' => $body]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPolicy()
    {
        return $this->client->get('/?policy');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deletePolicy()
    {
        return $this->client->delete('/?policy');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putReferer(array $body)
    {
        return $this->client->put('/?referer', [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getReferer()
    {
        return $this->client->get('/?referer');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putTagging(array $body)
    {
        return $this->client->put('/?tagging', [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTagging()
    {
        return $this->client->get('/?tagging');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteTagging()
    {
        return $this->client->delete('/?tagging');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putWebsite(array $body)
    {
        return $this->client->put('/?website', [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWebsite()
    {
        return $this->client->get('/?website');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteWebsite()
    {
        return $this->client->delete('/?website');
    }

    /**
     * @param  string  $id
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putInventory(string $id, array $body)
    {
        return $this->client->put('/?inventory', [
            'query' => \compact('id'),
            'body' => XML::build($body),
        ]);
    }

    /**
     * @param  string  $id
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getInventory(string $id)
    {
        return $this->client->get('/?inventory', [
            'query' => \compact('id'),
        ]);
    }

    /**
     * @param  string|null  $nextContinuationToken
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listInventoryConfigurations(?string $nextContinuationToken = null)
    {
        return $this->client->get('/?inventory', [
           'query' => ['continuation-token' => $nextContinuationToken]
        ]);
    }

    /**
     * @param  string  $id
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteInventory(string $id)
    {
        return $this->client->delete('/?inventory', [
            'query' => \compact('id'),
        ]);
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putVersioning(array $body)
    {
        return $this->client->put('/?versioning', [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getVersioning()
    {
        return $this->client->get('/?versioning');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putReplication(array $body)
    {
        return $this->client->put('/?replication', [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getReplication()
    {
        return $this->client->get('/?replication');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteReplication()
    {
        return $this->client->delete('/?replication');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putLogging(array $body)
    {
        return $this->client->put('/?logging', [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLogging()
    {
        return $this->client->get('/?logging');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putAccelerate(array $body)
    {
        return $this->client->put('/?accelerate', [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccelerate()
    {
        return $this->client->get('/?accelerate');
    }

    /**
     * @param  array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putEncryption(array $body)
    {
        return $this->client->put('/?encryption', [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEncryption()
    {
        return $this->client->get('/?encryption');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteEncryption()
    {
        return $this->client->delete('/?encryption');
    }
}
