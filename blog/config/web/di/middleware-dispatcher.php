<?php

declare(strict_types=1);


/**
 * @var array $params
 */

use Vjik\InputHttp\ParametersResolver\InputAttributeParametersResolver;
use Vjik\InputHttp\ParametersResolver\RequestModelParametersResolver;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\Reference;
use Yiisoft\Middleware\Dispatcher\CompositeParametersResolver;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;

return [
    ParametersResolverInterface::class => DynamicReference::to([
        'class' => CompositeParametersResolver::class,
        '__construct()' => [
            Reference::to(InputAttributeParametersResolver::class),
            Reference::to(RequestModelParametersResolver::class),
        ],
    ]),
];
