<?php

declare(strict_types=1);

namespace App\RouteAttribute;

use Vjik\InputHydrator\Context;
use Vjik\InputHydrator\NotResolvedException;
use Vjik\InputHydrator\ParameterAttributeInterface;
use Vjik\InputHydrator\ParameterAttributeResolverInterface;
use Vjik\InputHydrator\UnexpectedAttributeException;
use Yiisoft\Router\CurrentRoute;

final class RouteResolver implements ParameterAttributeResolverInterface
{
    public function __construct(private CurrentRoute $currentRoute)
    {
    }

    public function getParameterValue(ParameterAttributeInterface $attribute, Context $context): mixed
    {
        if (!$attribute instanceof Route) {
            throw new UnexpectedAttributeException(Route::class, $attribute);
        }

        return $this->currentRoute->getArgument($attribute->getName()) ?? throw new NotResolvedException();
    }
}
