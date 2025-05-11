<?php

namespace Overtrue\CosClient\Tests;

use GuzzleHttp\Psr7\Request;
use Overtrue\CosClient\Signature;

class SignatureTest extends TestCase
{
    public function test_getTimeSegments()
    {
        $signature = new Signature('mock-access-key', 'mock-secret-key');

        $request = new Request('GET', 'https://example.com');

        $timezone = \date_default_timezone_get();

        date_default_timezone_set('Asia/Shanghai');

        $asserts = [
            '900' => time() + 900,
            60 => time() + 60,
            time() + 3600 => time() + 3600,
            '+ 1 hour' => strtotime('+ 1 hour'),
            '+600 seconds' => strtotime('+600 seconds'),
            '2021-01-01T00:00:00Z' => strtotime('2021-01-01T00:00:00Z'),
            '2021-01-01' => strtotime('2021-01-01'),
            '2021-01-01 00:00:00' => strtotime('2021-01-01 00:00:00'),
        ];

        foreach ($asserts as $input => $output) {
            $expect = sprintf('q-sign-time=%d;%d&q-key-time=%d;%d', time() - 60, $output, time() - 60, $output);
            $this->assertStringContainsString($expect, $signature->createAuthorizationHeader($request, $input));
        }

        // date
        $date = \DateTimeImmutable::createFromFormat('Y-m-d 00:00:00', '2023-11-15 00:00:00');
        $expect = sprintf('q-sign-time=%d;%d&q-key-time=%d;%d', time() - 60, $date->getTimestamp(), time() - 60, $date->getTimestamp());

        $this->assertStringContainsString($expect, $signature->createAuthorizationHeader($request, $date));

        date_default_timezone_set($timezone);
    }

    public function test_getTimeSegments_with_null()
    {
        $signature = new Signature('mock-access-key', 'mock-secret-key');

        $request = new Request('GET', 'https://example.com');

        $timezone = \date_default_timezone_get();

        date_default_timezone_set('Asia/Shanghai');

        $output = strtotime('+60 minutes');
        $expect = sprintf('q-sign-time=%d;%d&q-key-time=%d;%d', time() - 60, $output, time() - 60, $output);

        $this->assertStringContainsString($expect, $signature->createAuthorizationHeader($request));
        $this->assertStringContainsString($expect, $signature->createAuthorizationHeader($request, null));

        date_default_timezone_set($timezone);
    }
}
