<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;

/* @var array $params */

return [
    Aliases::class => [
        'class' => Aliases::class,
        'constructor' => [$params['yiisoft/aliases']['aliases']],
    ],
];
