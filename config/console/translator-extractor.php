<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\TranslatorExtractor\Extractor;
use Yiisoft\TranslatorExtractor\CategorySource as ExtractorCategorySource;

/** @var array $params */

return [
    Extractor::class => [
        '__construct()' => [
            [
                DynamicReference::to([
                    'class' => ExtractorCategorySource::class,
                    '__construct()' => [
                        'app',
                        // Please set the following to use extractor.
                        // MessageReader and MessageWriter should be set to the SAME MessageSource.
                        DynamicReference::to(static function (Aliases $aliases) {
                            return new MessageSource($aliases->get('@messages'));
                        }),
                        DynamicReference::to(static function (Aliases $aliases) {
                            return new MessageSource($aliases->get('@messages'));
                        }),
                    ],
                ]),
            ],
        ],
    ],
];
