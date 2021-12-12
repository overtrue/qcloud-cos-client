<?php

namespace Overtrue\CosClient;

use Overtrue\CosClient\Exceptions\InvalidConfigException;
use Overtrue\CosClient\Support\XML;

class JobClient extends Client
{
    public function __construct(array|Config$config)
    {
        if (!($config instanceof Config)) {
            $config = new Config($config);
        }

        $this->validateConfig($config);

        parent::__construct($config->extend([
            'guzzle' => [
                'base_uri' => \sprintf(
                    'https://%s.cos-control.%s.myqcloud.com/',
                    $config->get('uin'),
                    $config->get('region')
                ),
                'headers' => [
                    'x-cos-appid' => $config->get('app_id')
                ]
            ]
        ]));
    }

    public function getJobs(array $query = []): Http\Response
    {
        return $this->get('/jobs', [
            'query' => $query,
        ]);
    }

    public function createJob(array $body): Http\Response
    {
        return $this->post('/jobs', [
            'body' => XML::fromArray($body),
        ]);
    }

    public function describeJob(string $id): Http\Response
    {
        return $this->get(\sprintf('/jobs/%s', $id));
    }

    public function updateJobPriority(string $id, int $priority): Http\Response
    {
        return $this->post(\sprintf('/jobs/%s/priority', $id), [
            'query' => [
                'priority' => $priority,
            ],
        ]);
    }

    public function updateJobStatus(string $id, array $query): Http\Response
    {
        return $this->post(\sprintf('/jobs/%s/status', $id), \compact('query'));
    }

    /**
     * @throws \Overtrue\CosClient\Exceptions\InvalidConfigException
     */
    protected function validateConfig(Config $config)
    {
        if (!$config->has('uin')) {
            throw new InvalidConfigException('Invalid config uin.');
        }

        if (!$config->has('region')) {
            throw new InvalidConfigException('Invalid config region.');
        }
    }
}
