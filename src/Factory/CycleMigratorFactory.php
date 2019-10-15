<?php

namespace App\Factory;

use Psr\Container\ContainerInterface;
use Spiral\Database\DatabaseManager;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\FileRepository;
use Spiral\Migrations\Migrator;

class CycleMigratorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $migConf = $container->get(MigrationConfig::class);

        $dbal = $container->get(DatabaseManager::class);

        $migrator = new Migrator($migConf, $dbal, new FileRepository($migConf));
        // Init migration table
        $migrator->configure();
        return $migrator;
    }
}
