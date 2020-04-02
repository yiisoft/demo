<?php

use Yiisoft\Composer\Config\Builder;
use Psr\Container\ContainerInterface;
use Yiisoft\Di\Container;
use Yiisoft\EventDispatcher\EventManager;
use Yiisoft\Http\Method;
use Yiisoft\Yii\Web\Application;
use Yiisoft\Yii\Web\SapiEmitter;
use Yiisoft\Yii\Web\ServerRequestFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/src/globals.php';
// Don't do it in production, assembling takes it's time
Builder::rebuild();
$startTime = microtime(true);
$container = new Container(
    require Builder::path('web', dirname(__DIR__)),
    require Builder::path('providers', dirname(__DIR__))
);
$container = $container->get(ContainerInterface::class);
$eventManager = $container->get(EventManager::class);
$eventManager->registerListeners(require Builder::path('events', dirname(__DIR__)));

$application = $container->get(Application::class);

$request = $container->get(ServerRequestFactory::class)->createFromGlobals();
$request = $request->withAttribute('applicationStartTime', $startTime);

try {
    $application->start();
    $response = $application->handle($request);
    $emitter = new SapiEmitter();
    $emitter->emit($response, $request->getMethod() === Method::HEAD);
} finally {
    $application->afterEmit($response);
    $application->shutdown();
}
