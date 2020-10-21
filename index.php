<?php

require __DIR__.'/vendor/autoload.php';

use GuzzleHttp\Middleware;
use Overtrue\CosClient\Client;

$client = new Client('AKIDsiQzQla780mQxLLU2GJC6rSt9FBv4bHs', 'b0GMH2c2NXWKxPhy77xhHgw2xtkT2Qdg');

$logger = new \Monolog\Logger('my-logger');

$logger->pushHandler(
    new \Monolog\Handler\RotatingFileHandler('/tmp/my-log.log')
);

$client->pushMiddleware(Middleware::log($logger, new \GuzzleHttp\MessageFormatter(\GuzzleHttp\MessageFormatter::DEBUG)));

$body = $client->get('https://cos.ap-guangzhou.myqcloud.com/', ['debug' => true])->getBody();

$body->rewind();
var_dump($body->getContents());
