<?php

use hiqdev\composer\config\Builder;
use yii\di\Container;
use yii\helpers\Yii;

(function () {
    require_once dirname(__DIR__, 4) . '/vendor/autoload.php';

    $container = new Container(require Builder::path('web'));

    //Yii::setContainer($container);

    $container->get('app')->run();
})();
