<?php

namespace App\Factory;

use App\Helper\CycleOrmHelper;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Psr\Container\ContainerInterface;
use Spiral\Database\DatabaseManager;
use Yiisoft\Cache\CacheInterface;

class CycleOrmFactory
{
    /** @var ContainerInterface */
    private $container;

    /** @var string */
    private $cacheKey = 'Cycle-ORM-Schema';

    public function __invoke(ContainerInterface $container)
    {
        $this->container = $container;
        $dbal = $container->get(DatabaseManager::class);

        $schema = $this->getSchema();

        return (new ORM(new Factory($dbal)))->withSchema($schema);
    }

    private function getSchema(): SchemaInterface
    {
        $cache = $this->container->get(CacheInterface::class);

        $array = $cache->getOrSet($this->cacheKey, function () {
            return $this->container->get(CycleOrmHelper::class)->getCurrentSchemaArray();
        });

        return new Schema($array);
    }
}
