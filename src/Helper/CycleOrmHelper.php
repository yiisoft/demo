<?php

namespace App\Helper;

use Cycle\Migrations\GenerateMigrations;
use Cycle\Annotated;
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
use Spiral\Database;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Migrator;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;
use Yiisoft\Aliases\Aliases;

class CycleOrmHelper
{
    /** @var Database\DatabaseManager $dbal */
    private $dbal;

    /** @var Aliases */
    private $aliases;

    /** @var string[] */
    private $entityPaths = [];

    private $tableNaming = Annotated\Entities::TABLE_NAMING_SINGULAR;

    public function __construct(Database\DatabaseManager $dbal, Aliases $aliases)
    {
        $this->aliases = $aliases;
        $this->dbal = $dbal;
    }

    /**
     * @param string|string[] $paths
     */
    public function addEntityPaths($paths)
    {
        $paths = (array)$paths;
        foreach ($paths as $path) {
            $this->entityPaths[] = $path;
        }
    }

    public function generateMigration(Migrator $migrator, MigrationConfig $config)
    {
        $classLocator = $this->getEntityClassLocator();

        // autoload annotations
        AnnotationRegistry::registerLoader('class_exists');

        (new Compiler())->compile(new Registry($this->dbal), [
            new Annotated\Embeddings($classLocator),   // register embeddable entities
            new Annotated\Entities($classLocator, null, $this->tableNaming), // register annotated entities
            new ResetTables(),                         // re-declared table schemas (remove columns)
            new GenerateRelations(),                   // generate entity relations
            new ValidateEntities(),                    // make sure all entity schemas are correct
            new RenderTables(),                        // declare table schemas
            new RenderRelations(),                     // declare relation keys and indexes
            new GenerateMigrations($migrator->getRepository(),$config), // generate migrations
            new GenerateTypecast(),                    // typecast non string columns
        ]);
    }

    public function getCurrentSchemaArray(): array
    {
        $classLocator = $this->getEntityClassLocator();

        // autoload annotations
        AnnotationRegistry::registerLoader('class_exists');

        return (new Compiler())->compile(new Registry($this->dbal), [
            new Annotated\Embeddings($classLocator),    // register embeddable entities
            new Annotated\Entities($classLocator, null, $this->tableNaming), // register annotated entities
            new ResetTables(),                          // re-declared table schemas (remove columns)
            new GenerateRelations(),                    // generate entity relations
            new ValidateEntities(),                     // make sure all entity schemas are correct
            new RenderTables(),                         // declare table schemas
            new RenderRelations(),                      // declare relation keys and indexes
            new SyncTables(),                           // sync table changes to database
            new GenerateTypecast(),                     // typecast non string columns
        ]);
    }

    private function getEntityClassLocator(): ClassLocator
    {
        $list = [];
        foreach ($this->entityPaths as $path) {
            $list[] = $this->aliases->get($path);
        }
        $finder = (new Finder())
            ->files()
            ->in($list);

        return new ClassLocator($finder);
    }
}
