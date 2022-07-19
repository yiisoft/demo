<?php

declare(strict_types=1);

use App\Auth\Identity;
use App\Auth\IdentityRepository;
use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

/** @var array $params */

return [
    IdentityRepositoryInterface::class => static function (ContainerInterface $container): IdentityRepository {
        return $container
            ->get(ORMInterface::class)
            ->getRepository(Identity::class);
    },
];
