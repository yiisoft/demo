<?php

declare(strict_types=1);

use Yiisoft\DataResponse\Formatter\HtmlDataResponseFormatter;
use Yiisoft\DataResponse\Formatter\XmlDataResponseFormatter;
use Yiisoft\DataResponse\Formatter\JsonDataResponseFormatter;

return [
    'yiisoft/data-response' => [
        'contentFormatters' => [
            'text/html' => HtmlDataResponseFormatter::class,
            'application/xml' => XmlDataResponseFormatter::class,
            'application/json' => JsonDataResponseFormatter::class,
        ],
    ],
];
