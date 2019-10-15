<?php


namespace App\Console\Command;

use App\Entity\User;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateUser extends Command
{
    private const EXIT_CODE_FAILED_TO_PERSIST = 1;

    private $orm;

    protected static $defaultName = 'user/create';

    public function __construct(ORMInterface $orm)
    {
        parent::__construct();
        $this->orm = $orm;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Creates a user')
            ->setHelp('.')
            ->addOption('id', 'id', InputOption::VALUE_REQUIRED, 'ID')
            ->addOption('token', 't', InputOption::VALUE_REQUIRED, 'Token')
            ->addOption('login', 'l', InputOption::VALUE_REQUIRED, 'Login')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $id = $input->getOption('id');
        $token = $input->getOption('token');
        $login = $input->getOption('login');
        $password = $input->getOption('password');

        $user = new User($id, $login);
        $user->setPassword($password);
        $user->setToken($token);

        $transaction = new Transaction($this->orm);
        $transaction->persist($user);

        try {
            $transaction->run();
        } catch (\Throwable $t) {
            $io->error($t->getMessage());
            return self::EXIT_CODE_FAILED_TO_PERSIST;
        }
    }
}
