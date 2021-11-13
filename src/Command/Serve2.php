<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Yii\Console\ExitCode;

use function explode;
use function fclose;
use function file_exists;
use function fsockopen;
use function getcwd;
use function is_dir;
use function passthru;
use function strpos;

final class Serve2 extends Command
{
    public const EXIT_CODE_NO_DOCUMENT_ROOT = 2;
    public const EXIT_CODE_NO_ROUTING_FILE = 3;
    public const EXIT_CODE_ADDRESS_TAKEN_BY_ANOTHER_PROCESS = 5;

    private const DEFAULT_PORT = '8080';
    private const DEFAULT_DOCROOT = 'public';
    private const DEFAULT_ROUTER = 'public/index.php';

    protected static $defaultName = 'serve';
    protected static $defaultDescription = 'Runs PHP built-in web server';

    public function configure(): void
    {
        $this
            ->setHelp('In order to access server from remote machines use 0.0.0.0:8000. That is especially useful when running server in a virtual machine.')
            ->addArgument('address', InputArgument::OPTIONAL, 'Host to serve at', 'localhost')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Port to serve at', self::DEFAULT_PORT)
            ->addOption('docroot', 't', InputOption::VALUE_OPTIONAL, 'Document root to serve from', self::DEFAULT_DOCROOT)
            ->addOption('router', 'r', InputOption::VALUE_OPTIONAL, 'Path to router script', self::DEFAULT_ROUTER)
            ->addOption('env', 'e', InputOption::VALUE_OPTIONAL, 'It is only used for testing.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $address */
        $address = $input->getArgument('address');

        /** @var string $router */
        $router = $input->getOption('router');

        /** @var string $port */
        $port = $input->getOption('port');

        /** @var string $docroot */
        $docroot = $input->getOption('docroot');

        if ($router === self::DEFAULT_ROUTER && !file_exists(self::DEFAULT_ROUTER)) {
            $io->warning('Default router "' . self::DEFAULT_ROUTER . '" does not exist. Serving without router. URLs with dots may fail.');
            $router = null;
        }

        /** @var string $env */
        $env = $input->getOption('env');

        $documentRoot = getcwd() . '/' . $docroot; // TODO: can we do it better?

        if (strpos($address, ':') === false) {
            $address .= ':' . $port;
        }

        if (!is_dir($documentRoot)) {
            $io->error("Document root \"$documentRoot\" does not exist.");
            return self::EXIT_CODE_NO_DOCUMENT_ROOT;
        }

        if ($this->isAddressTaken($address)) {
            $io->error("http://$address is taken by another process.");
            return self::EXIT_CODE_ADDRESS_TAKEN_BY_ANOTHER_PROCESS;
        }

        if ($router !== null && !file_exists($router)) {
            $io->error("Routing file \"$router\" does not exist.");
            return self::EXIT_CODE_NO_ROUTING_FILE;
        }

        $output->writeLn("Server started on <href=http://$address/>http://$address/</>");
        $output->writeLn("Document root is \"$documentRoot\"");

        if ($router) {
            $output->writeLn("Routing file is \"$router\"");
        }

        $output->writeLn('Quit the server with CTRL-C or COMMAND-C.');

        if ($env === 'test') {
            return ExitCode::OK;
        }

        $output->writeln('"' . PHP_BINARY . '"' . " -S $address -t \"$documentRoot\" $router");

        $x = passthru('"' . PHP_BINARY . '"' . " -S $address -t \"$documentRoot\" $router");

        $output->writeln($x===null ? 'null' : ($x ? 'true' : 'false'));

        return ExitCode::OK;
    }

    /**
     * @param string $address The server address.
     *
     * @return bool If address is already in use.
     */
    private function isAddressTaken(string $address): bool
    {
        [$hostname, $port] = explode(':', $address);
        $fp = @fsockopen($hostname, (int)$port, $errno, $errstr, 3);

        if ($fp === false) {
            return false;
        }

        fclose($fp);
        return true;
    }
}
