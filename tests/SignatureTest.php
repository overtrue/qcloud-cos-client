<?php

namespace Overtrue\CosClient\Tests;

use GuzzleHttp\Psr7\Request;
use Overtrue\CosClient\Signature;

class SignatureTest extends TestCase
{
    public function test_get_time_segments()
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

    public function test_get_time_segments_with_null()
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

    public function headerValueProvider(): array
    {
        return [
            'space' => ['attachment; filename="a b.pdf"'],
            'tilde' => ['v1~beta'],
            'multibyte' => ['海报 1.md'],
            'rawurlencoded' => ['bucket.cos.ap-guangzhou.myqcloud.com/dir/a%20b~c.md'],
            'urlencoded' => ['bucket.cos.ap-guangzhou.myqcloud.com/dir/a+b%7Ec.md'],
        ];
    }

    /**
     * @dataProvider headerValueProvider
     */
    public function test_header_values_are_encoded_as_rfc3986(string $value)
    {
        $signature = new Signature('mock-access-key', 'mock-secret-key');
        $request = new Request('PUT', 'https://example.com/dir/target.md', [
            'Content-Disposition' => $value,
            'x-cos-copy-source' => $value,
        ]);

        $authorization = $signature->createAuthorizationHeader($request);
        \parse_str($authorization, $parts);

        // Independent implementation of the COS spec: header values are encoded per RFC 3986.
        $headers = \sprintf('content-disposition=%1$s&host=example.com&x-cos-copy-source=%1$s', \rawurlencode($value));
        $httpString = "put\n/dir/target.md\n\n{$headers}\n";
        $stringToSign = "sha1\n{$parts['q-sign-time']}\n".\sha1($httpString)."\n";
        $signKey = \hash_hmac('sha1', $parts['q-key-time'], 'mock-secret-key');

        $this->assertSame('content-disposition;host;x-cos-copy-source', $parts['q-header-list']);
        $this->assertSame(\hash_hmac('sha1', $stringToSign, $signKey), $parts['q-signature']);
    }
}
