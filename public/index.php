<?php
use hiqdev\composer\config\Builder;
use Yiisoft\Di\Container;

(function () {
    require_once dirname(__DIR__) . '/vendor/autoload.php';

    // Don't do it in production, assembling takes it's time
    Builder::rebuild();

    $container = new Container(require Builder::path('web'));

    $aliases = $container->get(\Yiisoft\Aliases\Aliases::class);
    $aliases->set('@web', __DIR__);
    $aliases->set('@base', dirname(__DIR__));
    $aliases->set('@views', dirname(__DIR__) . '/views');

    $container->get('app')->run();
})();
