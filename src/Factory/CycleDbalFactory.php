<?php

namespace App\Factory;

use Cycle\Annotated;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Psr\Container\ContainerInterface;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\DatabaseManager;
use Spiral\Database\Driver\SQLite\SQLiteDriver;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;
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
