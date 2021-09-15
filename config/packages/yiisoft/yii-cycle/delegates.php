<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Yiisoft\Yii\Cycle\Factory\RepositoryContainer;

return [
    static function (ContainerInterface $container): ContainerInterface {
        return new RepositoryContainer($container);
    },
];
