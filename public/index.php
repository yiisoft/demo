<?php

use hiqdev\composer\config\Builder;
use yii\di\Container;
//use yii\helpers\Yii;

(function () {
    require_once dirname(__DIR__) . '/vendor/autoload.php';

    // Don't do it in production, assembling takes it's time
    Builder::rebuild();

    $container = new Container(require Builder::path('web'));

    //Yii::setContainer($container);

    $container->get('app')->run();
})();
