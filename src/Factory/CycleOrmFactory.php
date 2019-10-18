<?php

namespace App\Factory;

use App\Helper\EntityFinderHelper;
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
use Spiral\Database\DatabaseManager;
use Spiral\Tokenizer\ClassLocator;

class CycleOrmFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $dbal = $container->get(DatabaseManager::class);
        $classLocator = $container->get(EntityFinderHelper::class)->getClassLocator();

        // autoload annotations
        AnnotationRegistry::registerLoader('class_exists');

        $schema = $this->getSchema($classLocator, $dbal);

        return (new ORM(new Factory($dbal)))->withSchema($schema);
    }

    private function getSchema(ClassLocator $classLocator, DatabaseManager $dbal): SchemaInterface
    {
        $schema = (new Compiler())->compile(new Registry($dbal), [
            new Annotated\Embeddings($classLocator),  // register embeddable entities
            // register annotated entities
            new Annotated\Entities($classLocator, null, Annotated\Entities::TABLE_NAMING_SINGULAR),
            new ResetTables(),        // re-declared table schemas (remove columns)
            new GenerateRelations(),  // generate entity relations
            new ValidateEntities(),   // make sure all entity schemas are correct
            new RenderTables(),       // declare table schemas
            new RenderRelations(),    // declare relation keys and indexes
            new SyncTables(),         // sync table changes to database
            new GenerateTypecast(),   // typecast non string columns
        ]);

        return new Schema($schema);
    }
}
