<?php

declare(strict_types=1);

/**
 * @var array $params
 */

use Vjik\InputHttp\ParametersResolver\InputAttributeParametersResolver;
use Vjik\InputHttp\ParametersResolver\ValidatedRequestModelParametersResolver;
use Vjik\InputHttp\Request\Catcher\RequestCatcherParametersResolver;
use Yiisoft\Definitions\Reference;
use Yiisoft\Middleware\Dispatcher\CompositeParametersResolver;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

return [
    ParametersResolverInterface::class => [
        'class' => CompositeParametersResolver::class,
        '__construct()' => [
            Reference::to(RequestCatcherParametersResolver::class),
            Reference::to(InputAttributeParametersResolver::class),
            Reference::to(ValidatedRequestModelParametersResolver::class),
        ],
    ],
];
