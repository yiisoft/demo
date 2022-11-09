<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use function dirname;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Yiisoft\Config\ConfigPaths;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Yii\Event\ListenerConfigurationChecker;
use Yiisoft\Yii\Runner\ConfigFactory;

class EventListenerConfigurationTest extends TestCase
{
    public function testConsoleListenerConfiguration(): void
    {
        $config = ConfigFactory::create(new ConfigPaths(dirname(__DIR__, 2), 'config'), $_ENV['YII_ENV']);

        $containerConfig = ContainerConfig::create()
            ->withDefinitions($config->get('console'));
        $container = (new Container($containerConfig))->get(ContainerInterface::class);
        $checker = $container->get(ListenerConfigurationChecker::class);
        $checker->check($config->get('events-console'));

        self::assertInstanceOf(ListenerConfigurationChecker::class, $checker);
    }
}
