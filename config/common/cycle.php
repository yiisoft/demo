<?php

declare(strict_types=1);

use Cycle\Database\DatabaseManager;
use Cycle\ORM\Collection\DoctrineCollectionFactory;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;

/** @var array $params */

return [
    // Replace Factory definition to redefine default collection type
    // Todo: remove with https://github.com/yiisoft/yii-cycle/issues/111
    FactoryInterface::class => static function (DatabaseManager $dbManager, Spiral\Core\FactoryInterface $factory) {
        return new Factory(
            $dbManager,
            null,
            $factory,
            new DoctrineCollectionFactory()
        );
    },
];
