<?php

declare(strict_types=1);

use Yiisoft\Assets\AssetManager;
use Yiisoft\Factory\Definition\Reference;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;

return [
    'yiisoft/view' => [
        'basePath' => '@views',
        'commonParameters' => [
            'assetManager' => Reference::to(AssetManager::class),
            'urlGenerator' => Reference::to(UrlGeneratorInterface::class),
            'currentRoute' => Reference::to(CurrentRoute::class),
        ],
        'theme' => [
            'pathMap' => [],
            'basePath' => '',
            'baseUrl' => '',
        ],
    ],
];
