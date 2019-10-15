<?php


use App\Console\Command\CreateUser;
use App\Factory\CycleMigratorFactory;
use Psr\Container\ContainerInterface;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Migrator;
use Yiisoft\Aliases\Aliases;

$params = $params ?? [];

return [

    CreateUser::class => CreateUser::class,

    // Cycle Migrations
    Migrator::class => new CycleMigratorFactory(),
    // Migration Config
    MigrationConfig::class => function (ContainerInterface $container) {
        $aliases = $container->get(Aliases::class);
        return new MigrationConfig([
            'directory' => $aliases->get('@src/Console/Migration'),
            'namespace' => 'App\Console\Migration',
            'table'     => 'migration',
            'safe'      => false,
        ]);
    }
];
