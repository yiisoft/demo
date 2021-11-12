<?php

declare(strict_types=1);

use Cycle\Database\DatabaseManager;
use Cycle\ORM\Collection\DoctrineCollectionFactory;
use Cycle\ORM\Entity\Macros\EventDrivenCommandGenerator;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Transaction\CommandGeneratorInterface;
use Psr\Container\ContainerInterface;

/** @var array $params */

return [
    // Cycle ORM
    ORMInterface::class => ORM::class,
    ORM::class => static fn(
        FactoryInterface $factory,
        SchemaInterface $schema,
        CommandGeneratorInterface $commandGenerator
    ) => new ORM($factory, $schema, $commandGenerator),

    CommandGeneratorInterface::class => EventDrivenCommandGenerator::class,

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
