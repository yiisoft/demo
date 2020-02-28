<?php

use hiqdev\composer\config\Builder;
use Yiisoft\Di\Container;
use Yiisoft\Http\Method;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\DebugServiceProvider;
use Yiisoft\Yii\Web\Application;
use Yiisoft\Yii\Web\SapiEmitter;
use Yiisoft\Yii\Web\ServerRequestFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Don't do it in production, assembling takes it's time
Builder::rebuild();

$container = new Container(require Builder::path('web'));

/**
 * @var array $params The variable is available after requiring config files.
 */

$debugEnabled = (bool)($params['debugger.enabled'] ?? false) && class_exists(Debugger::class);

if ($debugEnabled) {
    $debugProvider = new DebugServiceProvider();
    $container->addProvider($debugProvider);
}

require_once dirname(__DIR__) . '/src/globals.php';

$application = $container->get(Application::class);

$request = $container->get(ServerRequestFactory::class)->createFromGlobals();

try {
    $application->start();
    $response = $application->handle($request);
    $emitter = new SapiEmitter();
    $emitter->emit($response, $request->getMethod() === Method::HEAD);
} finally {
    $application->shutdown();
}
