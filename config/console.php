<?php


use App\Console\Command\CreateUser;
use App\Factory\CycleMigratorFactory;
use Psr\Container\ContainerInterface;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Migrator;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Yii\Console\Application;

$params = $params ?? [];

return [
    // Application::class => function (ContainerInterface $container) use ($params) {
    //     $application = new Application();
    //     $application->setCommandLoader(new ContainerCommandLoader($container, $params['commands']));
    //     return $application;
    // },

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
