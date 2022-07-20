<?php

declare(strict_types=1);

namespace App\User\Console;

use App\Auth\AuthService;
use App\User\UserLoginException;
use App\User\UserPasswordException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;
use Yiisoft\Rbac\Manager;
use Yiisoft\Yii\Console\ExitCode;

final class CreateCommand extends Command
{
    protected static $defaultName = 'user/create';

    public function __construct(private AuthService $authService, private Manager $manager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a user')
            ->setHelp('This command allows you to create a user')
            ->addArgument('login', InputArgument::REQUIRED, 'Login')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addArgument('isAdmin', InputArgument::OPTIONAL, 'Create user as admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $login = $input->getArgument('login');
        $password = $input->getArgument('password');
        $isAdmin = (bool)$input->getArgument('isAdmin');

        try {
            $user = $this->authService->signup($login, $password);

            if ($isAdmin) {
                $userId = $user->getId();

                if ($userId === null) {
                    throw new \LogicException('User Id is NULL');
                }

                $this->manager->assign('admin', $userId);
            }

            $io->success('User created');
        } catch (UserLoginException|UserPasswordException $exception) {
            $io->error($exception::class . ' ' . $exception->getMessage());
            return ExitCode::DATAERR;
        } catch (Throwable $t) {
            $io->error($t->getMessage());
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
        return ExitCode::OK;
    }
}
