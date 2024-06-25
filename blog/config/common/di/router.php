<?php

declare(strict_types=1);

use Yiisoft\Config\Config;
use Yiisoft\Csrf\CsrfMiddleware;
use Yiisoft\DataResponse\Middleware\FormatDataResponse;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\Group;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Router\RouteCollectionInterface;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\UrlGeneratorInterface;

/**
 * @var Config $config
 * @var array $params
 * @var array $params['yiisoft/router-fastroute']
 * @var bool $params['yiisoft/router-fastroute']['encodeRaw']
 * @var array $defaultArguments 
 * @psalm-suppress MixedArgument $routes
 */

return [
    UrlGeneratorInterface::class => [
        'class' => UrlGenerator::class,
        'setEncodeRaw()' => [$params['yiisoft/router-fastroute']['encodeRaw']],
        'setDefaultArgument()' => ['_language', 'en'],
        'reset' => function (array $defaultArguments = []) {
            $defaultArguments = ['_language', 'en'];
        },
    ],
    
    RouteCollectionInterface::class => static function (RouteCollectorInterface $collector) use ($config) {
        $routes = $config->get('routes');
        $collector
            ->middleware(CsrfMiddleware::class)
            ->middleware(FormatDataResponse::class)
            ->addGroup(                
                Group::create('/{_language}')
                    ->routes(...$routes)
            );
        return new RouteCollection($collector);
    },
];