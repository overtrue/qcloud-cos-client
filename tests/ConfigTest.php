<?php

namespace Overtrue\CosClient\Tests;

use Overtrue\CosClient\Config;

class ConfigTest extends TestCase
{
    public function test_get()
    {
        $config = new Config(['option' => 'value']);

        $this->assertSame('value', $config->get('option'));
        $this->assertSame('value', $config['option']);
    }

    public function test_set()
    {
        $config = new Config(['option' => 'value']);

        $this->assertSame('value', $config->get('option'));

        $config->set('option', 'updated_value');

        $this->assertSame('updated_value', $config->get('option'));
    }

    public function test_has()
    {
        $config = new Config(['option' => 'value']);

        $this->assertTrue($config->has('option'));
        $this->assertFalse($config->has('non-exist-option'));
    }

    public function test_extend()
    {
        $config = (new Config(['option' => 'value']))->extend(['foo' => 'bar']);

        $this->assertSame('value', $config->get('option'));
        $this->assertSame('bar', $config->get('foo'));
    }
}
