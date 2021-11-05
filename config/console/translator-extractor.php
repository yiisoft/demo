<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\TranslatorExtractor\Extractor;

/** @var array $params */

return [
    Extractor::class => [
        '__construct()' => [
            // Please set the following to use extractor.
            // MessageReader and MessageWriter should be set to the SAME MessageSource.
            'messageReader' => DynamicReference::to(static function (Aliases $aliases) {
                return new MessageSource($aliases->get('@messages'));
            }),
            'messageWriter' => DynamicReference::to(static function (Aliases $aliases) {
                return new MessageSource($aliases->get('@messages'));
            }),
        ],
    ],
];
