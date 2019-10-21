<?php

namespace App\Factory;

use App\Helper\CycleOrmHelper;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Psr\Container\ContainerInterface;
use Spiral\Database\DatabaseManager;

class CycleOrmFactory
{
    /** @var ContainerInterface */
    private $container;

    public function __invoke(ContainerInterface $container)
    {
        $this->container = $container;
        $dbal = $container->get(DatabaseManager::class);

        $schema = new Schema($this->container->get(CycleOrmHelper::class)->getCurrentSchemaArray());

        return (new ORM(new Factory($dbal)))->withSchema($schema);
    }
}
