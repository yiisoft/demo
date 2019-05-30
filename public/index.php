<?php

use hiqdev\composer\config\Builder;
use yii\di\Container;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/globals.php';

// $config = require Builder::path('web');
$config = require dirname(__DIR__) . '/config/container.php';
$container = new Container($config);

(new \yii\web\Application(
    $container->get(\yii\web\ServerRequestFactory::class),
    $container->get(\yii\web\MiddlewareDispatcher::class),
    $container->get(\yii\web\emitter\EmitterInterface::class)
))->run();
