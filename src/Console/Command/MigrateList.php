<?php
namespace App\Console\Command;

use Spiral\Database;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\MigrationInterface;
use Spiral\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateList extends Command
{
    protected static $defaultName = 'migrate/list';

    /** @var MigrationConfig */
    private $config;
    /** @var Migrator */
    private $migrator;
    /**
     * MigrateGenerateCommand constructor.
     * @param Migrator                 $migrator
     * @param MigrationConfig   $conf
     */
    public function __construct(Migrator $migrator, MigrationConfig $conf)
    {
        $this->config = $conf;
        $this->migrator = $migrator;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $list = $this->migrator->getMigrations();
        $output->writeln(count($list) . ' migrations found in ' . $this->config->getDirectory() . ':');

        $statuses = [-1 => 'undefined', 0 => 'pending', 1 => 'executed'];
        $list = $this->migrator->getMigrations();

        foreach ($list as $migration) {
            $state = $migration->getState();
            $output->writeln($state->getName() . ' [' . ($statuses[$state->getStatus()] ?? '?') . ']');
        }
    }
}
