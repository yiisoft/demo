<?php

namespace App\Command\User;

use App\Entity\User;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateCommand extends Command
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
            ->setHelp('This command allows you to create a user')
            ->addArgument('login', InputArgument::REQUIRED, 'Login')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $login = $input->getArgument('login');
        $password = $input->getArgument('password');

        $user = new User();
        $user->setLogin($login);
        $user->setPassword($password);

        try {
            $transaction = new Transaction($this->orm);
            $transaction->persist($user);
            $transaction->run();
            $io->success('User created');
        } catch (\Throwable $t) {
            $io->error($t->getMessage());
            return self::EXIT_CODE_FAILED_TO_PERSIST;
        }
    }
}
