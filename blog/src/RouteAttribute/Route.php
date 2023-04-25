<?php

declare(strict_types=1);

namespace App\RouteAttribute;

use App\RouteAttribute\RouteResolver;
use Attribute;
use Vjik\InputHydrator\ParameterAttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::IS_REPEATABLE)]
final class Route implements ParameterAttributeInterface
{
    public function __construct(
        private string $name
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getResolver(): string
    {
        return RouteResolver::class;
    }

}
