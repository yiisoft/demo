<?php

declare(strict_types=1);

use App\User\User;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

return [
    IdentityRepositoryInterface::class => static function (ContainerInterface $container) {
        return $container->get(ORMInterface::class)->getRepository(User::class);
    },
];
