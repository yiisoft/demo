<?php

use Psr\Container\ContainerInterface;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Di\Container;
use Yiisoft\Http\Method;
use Yiisoft\Yii\Web\Application;
use Yiisoft\Yii\Event\EventConfigurator;
use Yiisoft\Yii\Web\SapiEmitter;
use Yiisoft\Yii\Web\ServerRequestFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Don't do it in production, assembling takes it's time
Builder::rebuild();
$startTime = microtime(true);
$container = new Container(
    require Builder::path('web'),
    require Builder::path('providers-web')
);
$container = $container->get(ContainerInterface::class);

$eventConfigurator = $container->get(EventConfigurator::class);
$eventConfigurator->registerListeners(require Builder::path('events-web', dirname(__DIR__)));

$application = $container->get(Application::class);

$request = $container->get(ServerRequestFactory::class)->createFromGlobals();
$request = $request->withAttribute('applicationStartTime', $startTime);

try {
    $application->start();
    $response = $application->handle($request);
    $emitter = new SapiEmitter();
    $emitter->emit($response, $request->getMethod() === Method::HEAD);
} finally {
    $application->afterEmit($response ?? null);
    $application->shutdown();
}
