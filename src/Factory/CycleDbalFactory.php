<?php

namespace App\Factory;

use Psr\Container\ContainerInterface;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\DatabaseManager;
use Spiral\Database\Driver\SQLite\SQLiteDriver;
use Yiisoft\Aliases\Aliases;

class CycleDbalFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $aliases = $container->get(Aliases::class);
        $databasePath = $aliases->get('@runtime/database.db');

        $dbal = new DatabaseManager(
            new DatabaseConfig([
                'default' => 'default',
                'databases' => [
                    'default' => ['connection' => 'sqlite']
                ],
                'connections' => [
                    'sqlite' => [
                        'driver' => SQLiteDriver::class,
                        'connection' => 'sqlite:' . $databasePath,
                        'username' => '',
                        'password' => '',
                    ]
                ]
            ])
        );

        return $dbal;
    }
}
