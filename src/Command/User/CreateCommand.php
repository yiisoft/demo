<?php

namespace App\Command\User;

use App\Entity\User;
use Cycle\ORM\Transaction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Cycle\Command\CycleDependencyPromise;

class CreateCommand extends Command
{
    private CycleDependencyPromise $promise;

    protected static $defaultName = 'user/create';

    public function __construct(CycleDependencyPromise $promise)
    {
        $this->promise = $promise;
        parent::__construct();
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

        $user = new User($login, $password);
        try {
            $transaction = new Transaction($this->promise->getORM());
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
