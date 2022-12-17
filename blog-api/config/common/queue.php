<?php

use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/* @var array $params */

return [
    AbstractConnection::class => [
        'class' => AMQPStreamConnection::class,
        '__construct()' => [
            'host' => 'rabbitmq',
            'port' => '5672',
            'user' => 'app',
            'password' => 'password',
            'vhost' => 'app'
        ],
    ]
];
