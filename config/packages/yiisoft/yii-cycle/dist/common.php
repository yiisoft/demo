<?php

declare(strict_types=1);

use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface as CycleFactoryInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Psr\Container\ContainerInterface;
use Spiral\Core\FactoryInterface as SpiralFactoryInterface;
use Spiral\Database\DatabaseManager;
use Spiral\Database\DatabaseProviderInterface;
use Yiisoft\Injector\Injector;
use Yiisoft\Yii\Cycle\Exception\SchemaWasNotProvidedException;
use Yiisoft\Yii\Cycle\Factory\CycleDynamicFactory;
use Yiisoft\Yii\Cycle\Factory\DbalFactory;
use Yiisoft\Yii\Cycle\Factory\OrmFactory;
use Yiisoft\Yii\Cycle\Schema\Conveyor\AnnotatedSchemaConveyor;
use Yiisoft\Yii\Cycle\Schema\Provider\Support\SchemaProviderPipeline;
use Yiisoft\Yii\Cycle\Schema\SchemaConveyorInterface;
use Yiisoft\Yii\Cycle\Schema\SchemaProviderInterface;

/**
 * @var array $params
 */

return [
    // Cycle DBAL
    DatabaseManager::class => new DbalFactory($params['yiisoft/yii-cycle']['dbal']),
    DatabaseProviderInterface::class => static function (ContainerInterface $container) {
        return $container->get(DatabaseManager::class);
    },
    // Cycle ORM
    ORMInterface::class => new OrmFactory($params['yiisoft/yii-cycle']['orm-promise-factory']),
    // Spiral Core Factory
    SpiralFactoryInterface::class => static function (ContainerInterface $container) {
        return new CycleDynamicFactory($container->get(Injector::class));
    },
    // Factory for Cycle ORM
    CycleFactoryInterface::class => static function (ContainerInterface $container) {
        return new Factory(
            $container->get(DatabaseManager::class),
            null,
            $container->get(SpiralFactoryInterface::class),
            $container
        );
    },
    // Schema provider
    SchemaProviderInterface::class => static function (ContainerInterface $container) use (&$params) {
        return (new SchemaProviderPipeline($container))->withConfig($params['yiisoft/yii-cycle']['schema-providers']);
    },
    // Schema
    SchemaInterface::class => static function (ContainerInterface $container) {
        $schema = $container->get(SchemaProviderInterface::class)->read();
        if ($schema === null) {
            throw new SchemaWasNotProvidedException();
        }
        return new Schema($schema);
    },
    // Annotated Schema Conveyor
    SchemaConveyorInterface::class => static function (ContainerInterface $container) use (&$params) {
        $conveyor = new AnnotatedSchemaConveyor($container);
        $conveyor->addEntityPaths($params['yiisoft/yii-cycle']['annotated-entity-paths']);
        return $conveyor;
    },
];
