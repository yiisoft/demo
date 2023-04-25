<?php

declare(strict_types=1);

use Vjik\InputHttp\Attribute\RequestDataAttributeResolverProvider;
use Vjik\InputHttp\Attribute\RequestParameterAttributeResolverProvider;
use Vjik\InputHydrator\Attribute\CompositeDataAttributeResolverProvider;
use Vjik\InputHydrator\Attribute\CompositeParameterAttributeResolverProvider;
use Vjik\InputHydrator\Attribute\ContainerDataAttributeResolverProvider;
use Vjik\InputHydrator\Attribute\ContainerParameterAttributeResolverProvider;
use Vjik\InputHydrator\Hydrator;
use Vjik\InputHydrator\HydratorInterface;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\Reference;

return [
    HydratorInterface::class => [
        'class' => Hydrator::class,
        '__construct()' => [
            'parameterAttributeResolverProvider' => DynamicReference::to([
                'class' => CompositeParameterAttributeResolverProvider::class,
                '__construct()' => [
                    Reference::to(RequestParameterAttributeResolverProvider::class),
                    Reference::to(ContainerParameterAttributeResolverProvider::class),
                ],
            ]),
            'dataAttributeResolverProvider' => DynamicReference::to([
                'class' => CompositeDataAttributeResolverProvider::class,
                '__construct()' => [
                    Reference::to(RequestDataAttributeResolverProvider::class),
                    Reference::to(ContainerDataAttributeResolverProvider::class),
                ],
            ]),
        ],
    ],
];
