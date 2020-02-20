<?php

use hiqdev\composer\config\Builder;
use Yiisoft\Di\Container;
use Yiisoft\Yii\Web\Application;
use Yiisoft\Yii\Web\ServerRequestFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Don't do it in production, assembling takes it's time
Builder::rebuild();

$debugProvider = new \Yiisoft\Yii\Debug\DebugServiceProvider();
$container = new Container(require Builder::path('web'), [$debugProvider]);

require dirname(__DIR__) . '/src/globals.php';

$dispatcher = $container->get(\Psr\EventDispatcher\EventDispatcherInterface::class);
$event = $dispatcher->dispatch(new \Yiisoft\Yii\Debug\Event\ApplicationStartup());

try {
    $request = $container->get(ServerRequestFactory::class)->createFromGlobals();
    $container->get(Application::class)->handle($request);
} finally {
    $dispatcher->dispatch(new \Yiisoft\Yii\Debug\Event\ApplicationShutdown());
}
