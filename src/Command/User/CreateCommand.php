<?php

namespace App\Command\User;

use App\Entity\User;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Yii\Console\ExitCode;

class CreateCommand extends Command
{
    private ORMInterface $orm;

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

    protected function execute(InputInterface $input, OutputInterface $output): int
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
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
        return ExitCode::OK;
    }
}
