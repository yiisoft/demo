<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as GuzzleClientAdapter;
use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Yiisoft\Definitions\Reference;

return [
    HttpClient::class => GuzzleClient::class,
    HttpAsyncClient::class => [
        'class' => GuzzleClientAdapter::class,
        '__construct()' => [
            Reference::to(HttpClient::class),
        ],
    ],
];
