<?php

declare(strict_types=1);

return [
    static function (Psr\Container\ContainerInterface $container) {
        $routeRegistrar = $container->get(\Yiisoft\Router\RouteAttributesRegistrarInterface::class);
        $routeRegistrar->register();
    },
];
