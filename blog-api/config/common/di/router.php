<?php

declare(strict_types=1);

use App\Infrastructure\Http\Middleware\ExceptionMiddleware;
use Yiisoft\Config\Config;
use Yiisoft\DataResponse\Middleware\FormatDataResponse;
use Yiisoft\Request\Body\RequestBodyParser;
use Yiisoft\Router\FastRoute\UrlGenerator;
use Yiisoft\Router\Group;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Router\RouteCollectionInterface;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\UrlGeneratorInterface;

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
            ->middleware(FormatDataResponse::class)
            ->middleware(ExceptionMiddleware::class)
            ->middleware(RequestBodyParser::class)
            ->addGroup(
                Group::create('/{_language}')->routes(...$config->get('app-routes')),
            )
            ->addGroup(
                Group::create()->routes(...$config->get('routes')),
            );

        return new RouteCollection($collector);
    },
];
