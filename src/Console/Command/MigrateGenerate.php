<?php
namespace App\Console\Command;

use App\Helper\CycleOrmHelper;
use Spiral\Migrations\Migrator;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateGenerate extends Command
{
    protected static $defaultName = 'migrate/generate';

    /** @var Migrator */
    private $migrator;

    /** @var CycleOrmHelper */
    private $cycleHelper;

    /** @var MigrationConfig */
    private $config;

    public function __construct(
        Migrator $migrator,
        MigrationConfig $conf,
        CycleOrmHelper $cycleHelper
    ) {
        parent::__construct();
        $this->migrator = $migrator;
        $this->config = $conf;
        $this->cycleHelper = $cycleHelper;
    }

    public function configure(): void
    {
        $this->setDescription('Generates a migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        // check existing unapplied migrations
        $list = $this->migrator->getMigrations();
        foreach ($list as $migration) {
            if ($migration->getState()->getStatus() !== State::STATUS_EXECUTED) {
                $output->writeln('<fg=red>Outstanding migrations found, run `migrate/up` first.</fg=red>');
                return;
            }
        }

        $this->cycleHelper->generateMigration($this->migrator, $this->config);
    }
}
