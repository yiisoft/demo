<?php
use hiqdev\composer\config\Builder;
use Yiisoft\Di\Container;
use Yiisoft\Yii\Web\Application;

(function () {
    require_once dirname(__DIR__) . '/vendor/autoload.php';

    // Don't do it in production, assembling takes it's time
    Builder::rebuild();

    $container = new Container(require Builder::path('web'));
    $container->get(Application::class)->run();
})();
