<?php

declare(strict_types=1);

namespace App\User\Console;

use App\User\User;
use App\User\UserRepository;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;
use Yiisoft\Rbac\Manager;
use Yiisoft\Rbac\RolesStorageInterface;
use Yiisoft\Yii\Console\ExitCode;

final class CreateCommand extends Command
{
    private Manager $manager;
    private RolesStorageInterface $rolesStorage;
    private UserRepository $userRepository;

    protected static $defaultName = 'user/create';

    public function __construct(
        Manager $manager,
        RolesStorageInterface $rolesStorage,
        UserRepository $userRepository
    ) {
        $this->manager = $manager;
        $this->rolesStorage = $rolesStorage;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    public function configure(): void
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

        $user = new User($login, $password);
        try {
            $this->userRepository->save($user);

            if ($isAdmin) {
                $role = $this->rolesStorage->getRoleByName('admin');
                $userId = $user->getId();

                if ($role === null) {
                    throw new Exception('Role admin is NULL');
                }

                if ($userId === null) {
                    throw new Exception('User Id is NULL');
                }

                $this->manager->assign($role, $userId);
            }

            $io->success('User created');
        } catch (Throwable $t) {
            $io->error($t->getMessage());
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
        return ExitCode::OK;
    }
}
