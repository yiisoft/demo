<?php
namespace App\Console\Command;

use App\Helper\EntityFinderHelper;
use Cycle\Annotated;
use Cycle\Migrations\GenerateMigrations;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Spiral\Database;
use Spiral\Migrations\Migrator;
use Spiral\Migrations\Config\MigrationConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateGenerate extends Command
{
    protected static $defaultName = 'migrate/generate';

    /** @var Database\DatabaseManager $dbal */
    private $dbal;
    /** @var Migrator */
    private $migrator;
    /** @var EntityFinderHelper */
    private $entityFinder;
    /** @var MigrationConfig */
    private $config;

    public function __construct(
        Migrator $migrator,
        Database\DatabaseManager $dbal,
        MigrationConfig $conf,
        EntityFinderHelper $entityFinder
    ) {
        parent::__construct();
        $this->migrator = $migrator;
        $this->dbal = $dbal;
        $this->config = $conf;
        $this->entityFinder = $entityFinder;
    }

    public function configure(): void
    {
        $this->setDescription('Generates a migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $classLocator = $this->entityFinder->getClassLocator();

        // autoload annotations
        AnnotationRegistry::registerLoader('class_exists');

        $schema = (new Compiler())->compile(new Registry($this->dbal), [
            new Annotated\Embeddings($classLocator),   // register embeddable entities
            // register annotated entities
            new Annotated\Entities($classLocator, null, Annotated\Entities::TABLE_NAMING_SINGULAR),
            new ResetTables(),                         // re-declared table schemas (remove columns)
            new GenerateRelations(),                   // generate entity relations
            new ValidateEntities(),                    // make sure all entity schemas are correct
            new RenderTables(),                        // declare table schemas
            new RenderRelations(),                     // declare relation keys and indexes
            new GenerateMigrations(
                $this->migrator->getRepository(),
                $this->config
            ),                                         // generate migrations
            new GenerateTypecast(),                    // typecast non string columns
        ]);
    }
}
