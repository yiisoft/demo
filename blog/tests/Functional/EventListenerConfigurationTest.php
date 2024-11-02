<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Yiisoft\Config\Config;
use PHPUnit\Framework\TestCase;
use Yiisoft\Config\ConfigPaths;
use Yiisoft\Config\Modifier\RecursiveMerge;
use Yiisoft\Config\Modifier\ReverseMerge;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Yii\Event\ListenerConfigurationChecker;

class EventListenerConfigurationTest extends TestCase
{
    public function testConsoleListenerConfiguration(): void
    {
        $config = new Config(
            new ConfigPaths(dirname(__DIR__, 2), 'config'),
            $_ENV['YII_ENV'],
            [
                ReverseMerge::groups('events', 'events-web', 'events-console'),
                RecursiveMerge::groups('params', 'params-web', 'params-console', 'events', 'events-web', 'events-console'),
            ],
            'params-console',
        );

        $container = new Container(
            ContainerConfig::create()
                ->withDefinitions($config->get('di-console'))
                ->withProviders($config->get('di-providers-console'))
        );

        $checker = $container->get(ListenerConfigurationChecker::class);

        $checker->check($config->get('events-console'));
        $checker->check($config->get('events-web'));

        self::assertInstanceOf(ListenerConfigurationChecker::class, $checker);
    }
}
