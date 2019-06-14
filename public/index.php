<?php

use hiqdev\composer\config\Builder;
use yii\di\Container;
//use yii\helpers\Yii;

(function () {
    require_once dirname(__DIR__) . '/vendor/autoload.php';

    // Don't do it in production, assembling takes it's time
    Builder::rebuild();

    $container = new Container(require Builder::path('web'));

    /* @var \yii\base\Aliases $aliases */
    $aliases = $container->get(\yii\base\Aliases::class);
    $aliases->set('@web', __DIR__);
    $aliases->set('@base', dirname(__DIR__));
    $aliases->set('@views', dirname(__DIR__) . '/views');

    //Yii::setContainer($container);

    $container->get('app')->run();
})();
