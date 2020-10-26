<?php

namespace Overtrue\CosClient\Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
