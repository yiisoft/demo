<?php

declare(strict_types=1);

use Yiisoft\Translator\Extractor\Command\ExtractCommand;

return [
    'yiisoft/yii-console' => [
        'commands' => [
            'translator/extract' => ExtractCommand::class,
        ],
    ],
];
