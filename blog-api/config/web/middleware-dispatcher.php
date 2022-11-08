<?php

declare(strict_types=1);

use Yiisoft\Middleware\Dispatcher\WrapperFactoryInterface;
use Yiisoft\RequestModel\WrapperFactory;

/**
 * @var array $params
 */

return [
    WrapperFactoryInterface::class => WrapperFactory::class,
];
