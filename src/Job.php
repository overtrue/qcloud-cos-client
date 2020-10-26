<?php

namespace Overtrue\CosClient;

use InvalidArgumentException;

class Job extends Client
{
    /**
     * @param  \Overtrue\CosClient\Config  $config
     */
    public function __construct(Config $config)
    {
        $this->validate($config);

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

    /**
     * @param array $query
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function lists(array $query)
    {
        return $this->get('/jobs', [
            'query' => $query,
        ]);
    }

    /**
     * @param array $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create(array $body)
    {
        return $this->post('/jobs', [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @param string $id
     * @param array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function describe(string $id, array $body)
    {
        return $this->post(\sprintf('/jobs/%s', $id), [
            'body' => XML::build($body),
        ]);
    }

    /**
     * @param string $id
     * @param int    $priority
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updatePriority(string $id, int $priority)
    {
        return $this->post(\sprintf('/jobs/%s/priority', $id), [
            'query' => [
                'priority' => $priority,
            ],
        ]);
    }

    /**
     * @param string $id
     * @param array  $body
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function updateStatus(string $id, array $body)
    {
        return $this->post(\sprintf('/jobs/%s/status', $id), [
            'query' => $body,
        ]);
    }

    /**
     * @param \Overtrue\CosClient\Config $config
     *
     * @return bool
     */
    protected function validate(Config $config)
    {
        if (!$config->get('uin')) {
            throw new InvalidArgumentException('Invalid config uin.');
        }

        if (!$config->get('region')) {
            throw new InvalidArgumentException('Invalid config region.');
        }

        return true;
    }
}
