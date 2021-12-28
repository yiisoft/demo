<?php

declare(strict_types=1);

use Cycle\Database\DatabaseManager;
use Cycle\ORM\Collection\DoctrineCollectionFactory;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;

/** @var array $params */

return [
    // Factory for Cycle ORM
    FactoryInterface::class => static function (DatabaseManager $dbManager, \Spiral\Core\FactoryInterface $factory) {
        return new Factory(
            $dbManager,
            null,
            $factory,
            new DoctrineCollectionFactory()
        );
    },
];
