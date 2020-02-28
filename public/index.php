<?php

use hiqdev\composer\config\Builder;
use Yiisoft\Di\Container;
use Yiisoft\Http\Method;
use Yiisoft\Yii\Web\Application;
use Yiisoft\Yii\Web\Emitter\EmitterInterface;
use Yiisoft\Yii\Web\ServerRequestFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Don't do it in production, assembling takes it's time
Builder::rebuild();

$container = new Container(require Builder::path('web'));

/**
 * @var array $params The variable is available after requiring config files.
 */

$debugEnabled = (bool)($params['debugger.enabled'] ?? false) && class_exists(\Yiisoft\Yii\Debug\Debugger::class);

if ($debugEnabled) {
    $debugProvider = new \Yiisoft\Yii\Debug\DebugServiceProvider();
    $container->addProvider($debugProvider);
}

require_once dirname(__DIR__) . '/src/globals.php';

$application = $container->get(Application::class);
$emitter = $container->get(EmitterInterface::class);

$request = $container->get(ServerRequestFactory::class)->createFromGlobals();

try {
    $application->start();
    $response = $application->handle($request);
    $emitter->emit($response, $request->getMethod() === Method::HEAD);
} finally {
    $application->shutdown();
}
