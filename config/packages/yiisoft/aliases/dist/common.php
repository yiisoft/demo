<?php

declare(strict_types=1);

/* @var array $params */

use Yiisoft\Aliases\Aliases;

return [
    Aliases::class => [
        'class' => Aliases::class,
        '__construct()' => [$params['yiisoft/aliases']['aliases']],
    ],
];
