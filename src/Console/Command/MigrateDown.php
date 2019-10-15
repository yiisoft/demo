<?php
namespace App\Console\Command;

use Spiral\Database;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\MigrationInterface;
use Spiral\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateDown extends Command
{
    protected static $defaultName = 'migrate/down';

    /** @var Database\DatabaseManager $dbal */
    protected $dbal;
    /** @var MigrationConfig */
    private $config;
    /** @var Migrator */
    private $migrator;
    /**
     * MigrateGenerateCommand constructor.
     * @param Migrator                 $migrator
     * @param Database\DatabaseManager $dbal
     * @param MigrationConfig   $conf
     */
    public function __construct(Migrator $migrator, Database\DatabaseManager $dbal, MigrationConfig $conf)
    {
        $this->dbal = $dbal;
        $this->config = $conf;
        $this->migrator = $migrator;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $list = $this->migrator->getMigrations();
        $output->writeln(count($list) . ' migrations found in ' . $this->config->getDirectory());

        $limit = PHP_INT_MAX;
        $statuses = [-1 => 'undefined', 0 => 'pending', 1 => 'executed'];
        try {
            $migration = $this->migrator->rollback();
            if (!$migration instanceof MigrationInterface)
                throw new \Exception('Migration not found');

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
