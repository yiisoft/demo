<?php

use hiqdev\composer\config\Builder;
use yii\di\Container;
use yii\helpers\Yii;

require dirname(__DIR__) . '/src/globals.php';

(function () {
    require_once dirname(__DIR__, 4) . '/vendor/autoload.php';

    $container = new Container(require Builder::path('web'));

    Yii::setContainer($container);

    $container->get('app')->run();
})();
