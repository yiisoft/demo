<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Migrator;
use Yiisoft\Yii\Cycle\Command\CycleDependencyProxy;
use Yiisoft\Yii\Cycle\Factory\MigrationConfigFactory;
use Yiisoft\Yii\Cycle\Factory\MigratorFactory;

/**
 * @var array $params
 */

return [
    Migrator::class => new MigratorFactory(),
    MigrationConfig::class => new MigrationConfigFactory($params['yiisoft/yii-cycle']['migrations']),
    CycleDependencyProxy::class => static function (ContainerInterface $container) {
        return new CycleDependencyProxy($container);
    },
];
