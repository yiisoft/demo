<?php

declare(strict_types=1);

use Yiisoft\DataResponse\DataResponseFactory;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\DataResponse\DataResponseFormatterInterface;
use Yiisoft\DataResponse\Formatter\HtmlDataResponseFormatter;
use Yiisoft\DataResponse\Middleware\ContentNegotiator;
use Yiisoft\Definitions\DynamicReferencesArray;

/* @var $params array */

return [
    DataResponseFormatterInterface::class => HtmlDataResponseFormatter::class,
    DataResponseFactoryInterface::class => DataResponseFactory::class,
    ContentNegotiator::class => [
        '__construct()' => [
            'contentFormatters' => DynamicReferencesArray::from($params['yiisoft/data-response']['contentFormatters']),
        ],
    ],
];
