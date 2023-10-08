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
use Yiisoft\Yii\Debug\Viewer\Middleware\ToolbarMiddleware;

/**
 * @var Config $config
 * @var array $params
 */

return [
    UrlGeneratorInterface::class => [
        'class' => UrlGenerator::class,
        'setEncodeRaw()' => [$params['yiisoft/router-fastroute']['encodeRaw']],
        'setDefaultArgument()' => ['_language', 'en'],
        'reset' => function () {
            $this->defaultArguments = ['_language', 'en'];
        },
    ],

    RouteCollectionInterface::class => static function (RouteCollectorInterface $collector) use ($config) {
        $collector
            ->middleware(CsrfMiddleware::class)
            ->middleware(FormatDataResponse::class)
            ->addGroup(
                Group::create('/{_language}')->routes(...$config->get('app-routes')),
            )
            ->addGroup(
                Group::create()->routes(...$config->get('routes')),
            );

        if (!str_starts_with(getenv('YII_ENV') ?: '', 'prod')) {
            $collector->middleware(ToolbarMiddleware::class);
        }

        return new RouteCollection($collector);
    },
];
