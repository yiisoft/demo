<?php

declare(strict_types=1);

use Vjik\InputHttp\Attribute\RequestDataAttributeResolverProvider;
use Vjik\InputHttp\Attribute\RequestParameterAttributeResolverProvider;
use Vjik\InputHttp\ParametersResolver\ValidatedRequestModelParametersResolver;
use Vjik\InputHydrator\Attribute\CompositeDataAttributeResolverProvider;
use Vjik\InputHydrator\Attribute\CompositeParameterAttributeResolverProvider;
use Vjik\InputHydrator\Attribute\ContainerDataAttributeResolverProvider;
use Vjik\InputHydrator\Attribute\ContainerParameterAttributeResolverProvider;
use Vjik\InputHydrator\DataAttributeResolverProviderInterface;
use Vjik\InputHydrator\Hydrator;
use Vjik\InputHydrator\HydratorInterface;
use Vjik\InputHydrator\ParameterAttributeResolverProviderInterface;
use Vjik\InputValidation\ValidatingHydrator;
use Yiisoft\Definitions\Reference;

return [
    HydratorInterface::class => [
        'class' => Hydrator::class,
        '__construct()' => [
            'parameterAttributeResolverProvider' => Reference::to(ParameterAttributeResolverProviderInterface::class),
            'dataAttributeResolverProvider' => Reference::to(DataAttributeResolverProviderInterface::class),
        ],
    ],
    ParameterAttributeResolverProviderInterface::class => [
        'class' => CompositeParameterAttributeResolverProvider::class,
        '__construct()' => [
            Reference::to(RequestParameterAttributeResolverProvider::class),
            Reference::to(ContainerParameterAttributeResolverProvider::class),
        ],
    ],
    DataAttributeResolverProviderInterface::class => [
        'class' => CompositeDataAttributeResolverProvider::class,
        '__construct()' => [
            Reference::to(RequestDataAttributeResolverProvider::class),
            Reference::to(ContainerDataAttributeResolverProvider::class),
        ],
    ],
    ValidatedRequestModelParametersResolver::class => [
        '__construct()' => [
            'hydrator' => Reference::to(ValidatingHydrator::class),
        ],
    ],
];
