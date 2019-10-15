<?php
namespace App\Console\Command;

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
use Psr\Container\ContainerInterface;
use Spiral\Database;
use Spiral\Migrations\Migrator;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class MigrateGenerate extends Command
{
    protected static $defaultName = 'migrate/generate';

    /** @var Database\DatabaseManager $dbal */
    private $dbal;
    /** @var Migrator */
    private $migrator;
    /** @var ContainerInterface */
    private $container;
    /** @var MigrationConfig */
    private $config;

    public function __construct(Migrator $migrator, Database\DatabaseManager $dbal, MigrationConfig $conf, ContainerInterface $container)
    {
        parent::__construct();
        $this->migrator = $migrator;
        $this->dbal = $dbal;
        $this->config = $conf;
        $this->container = $container;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Generates a migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityPaths = $this->container->get('CycleEntityPaths');

        $finder = (new Finder())->files()->in($entityPaths);
        $classLocator = new ClassLocator($finder);

        // autoload annotations
        AnnotationRegistry::registerLoader('class_exists');

        $schema = (new Compiler())->compile(new Registry($this->dbal), [
            new Annotated\Embeddings($classLocator),                  # register embeddable entities
            new Annotated\Entities($classLocator),                    # register annotated entities
            new ResetTables(),                                        # re-declared table schemas (remove columns)
            new GenerateRelations(),                                  # generate entity relations
            new ValidateEntities(),                                   # make sure all entity schemas are correct
            new RenderTables(),                                       # declare table schemas
            new RenderRelations(),                                    # declare relation keys and indexes
            new GenerateMigrations(
                $this->migrator->getRepository(),
                $this->config
            ),                                                        # generate migrations
            new GenerateTypecast(),                                   # typecast non string columns
        ]);
    }
}
