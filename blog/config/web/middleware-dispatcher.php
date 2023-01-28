<?php

declare(strict_types=1);


/**
 * @var array $params
 */

use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;
use Yiisoft\RequestModel\HandlerParametersResolver;

return [
    ParametersResolverInterface::class => HandlerParametersResolver::class,
];
