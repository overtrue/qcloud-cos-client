<?php

namespace Overtrue\CosClient;

use ArrayAccess;
use JsonSerializable;

class Config implements ArrayAccess, JsonSerializable
{
    protected array $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function get(string $key, mixed $default = null)
    {
        $config = $this->options;

        if (isset($config[$key])) {
            return $config[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($config) || !array_key_exists($segment, $config)) {
                return $default;
            }
            $config = $config[$segment];
        }

        return $config;
    }

    public function set(string $key, $value): mixed
    {
        $keys = explode('.', $key);
        $config = &$this->options;

        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($config[$key]) || !is_array($config[$key])) {
                $config[$key] = [];
            }
            $config = &$config[$key];
        }

        $config[array_shift($keys)] = $value;

        return $config;
    }

    public function has(string $key): bool
    {
        return (bool) $this->get($key);
    }

    public function extend(array $options): Config
    {
        return new Config(\array_merge($this->options, $options));
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->options);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }

    public function jsonSerialize()
    {
        return $this->options;
    }

    public function __toString()
    {
        return \json_encode($this, \JSON_UNESCAPED_UNICODE);
    }
}
