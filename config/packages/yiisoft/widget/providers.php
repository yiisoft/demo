<?php

declare(strict_types=1);

use Yiisoft\Di\Container;
use Yiisoft\Di\Contracts\ServiceProviderInterface;
use Yiisoft\Widget\WidgetFactory;

return [
    'yiisoft/widget' => static function (): ServiceProviderInterface {
        return new class() implements ServiceProviderInterface {
            public function register(Container $container): void
            {
                WidgetFactory::initialize($container);
            }
        };
    },
];
