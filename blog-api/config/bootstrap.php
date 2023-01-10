<?php

declare(strict_types=1);

return [
    function (Psr\Container\ContainerInterface $container) {
        $urlGenerator = $container->get(\Yiisoft\Router\UrlGeneratorInterface::class);
        $urlGenerator->setUriPrefix($_ENV['BASE_URL']);
    },
];
