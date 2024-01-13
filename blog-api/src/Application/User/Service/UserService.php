<?php

declare(strict_types=1);

namespace App\Application\User\Service;

use App\Application\Exception\BadRequestException;
use App\Application\User\Entity\User;
use App\Infrastructure\Queue\LoggingAuthorizationHandler;
use App\Infrastructure\Queue\UserLoggedInMessage;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Definitions\Exception\InvalidConfigException;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\Queue\QueueFactoryInterface;

final class UserService
{
    public function __construct(
        private IdentityRepositoryInterface $identityRepository,
        private CurrentUser $currentUser,
        private QueueFactoryInterface $queueFactory,
    ) {
    }

    /**
     * @param string $login
     * @param string $password
     *
     * @throws BadRequestException
     * @throws InvalidConfigException
     * @return IdentityInterface
     */
    public function login(string $login, string $password): IdentityInterface
    {
        $identity = $this->identityRepository->findByLogin($login);
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
