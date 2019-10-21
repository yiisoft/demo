<?php
namespace App\Console\Command;

use App\Helper\CycleOrmHelper;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\MigrationInterface;
use Spiral\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateDown extends Command
{
    protected static $defaultName = 'migrate/down';

    /** @var MigrationConfig */
    private $config;

    /** @var Migrator */
    private $migrator;

    /** @var CycleOrmHelper */
    private $cycleOrmHelper;

    /**
     * MigrateGenerateCommand constructor.
     * @param Migrator        $migrator
     * @param MigrationConfig $conf
     * @param CycleOrmHelper  $cycleOrmHelper
     */
    public function __construct(Migrator $migrator, MigrationConfig $conf, CycleOrmHelper $cycleOrmHelper)
    {
        parent::__construct();
        $this->config = $conf;
        $this->migrator = $migrator;
        $this->cycleOrmHelper = $cycleOrmHelper;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Rollback last migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // drop cached schema
        $this->cycleOrmHelper->dropCurrentSchemaCache();

        $list = $this->migrator->getMigrations();
        $output->writeln(count($list) . ' migrations found in ' . $this->config->getDirectory());

        $statuses = [-1 => 'undefined', 0 => 'pending', 1 => 'executed'];
        try {
            $migration = $this->migrator->rollback();
            if (!$migration instanceof MigrationInterface) {
                throw new \Exception('Migration not found');
            }

            $state = $migration->getState();
            $status = $state->getStatus();
            $output->writeln($state->getName() . ': ' . ($statuses[$status] ?? $status));
        } catch (\Throwable $e) {
            $output->writeln([
                '===================',
                'Error!',
                $e->getMessage(),
            ]);
            return;
        }
    }
}
