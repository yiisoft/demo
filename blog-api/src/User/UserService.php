<?php

declare(strict_types=1);

namespace App\User;

use App\Exception\BadRequestException;
use App\Queue\LoggingAuthorizationHandler;
use App\Queue\UserLoggedInMessage;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\User\CurrentUser;
use Yiisoft\Queue\QueueFactoryInterface;

final class UserService
{
    public function __construct(
        private CurrentUser $currentUser,
        private IdentityRepositoryInterface $identityRepository,
        private QueueFactoryInterface $queueFactory,
    ) {
    }

    /**
     * @param string $login
     * @param string $password
     *
     * @throws InvalidConfigException
     * @throws BadRequestException
     *
     * @return IdentityInterface
     */
    public function login(string $login, string $password): IdentityInterface
    {
        /** @var UserRepository $identityRepository */
        $identityRepository = $this->identityRepository;
        $identity = $identityRepository->findByLogin($login);
        if ($identity === null) {
            throw new BadRequestException('No such user.');
        }

        if (!$identity->validatePassword($password)) {
            throw new BadRequestException('Invalid password.');
        }

        if (!$this->currentUser->login($identity)) {
            throw new BadRequestException();
        }

        $identity->resetToken();
        $this->identityRepository->save($identity);

        $queueMessage = new UserLoggedInMessage($identity->getId(), time());
        $this->queueFactory->get(LoggingAuthorizationHandler::CHANNEL)->push($queueMessage);

        return $identity;
    }

    public function logout(User $user): void
    {
        $user->resetToken();
        $this->identityRepository->save($user);
    }
}
