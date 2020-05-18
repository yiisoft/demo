<?php

use Yiisoft\Aliases\Aliases;

/**
 * @var array $params
 */

return [
    Aliases::class => [
        '__class' => Aliases::class,
        '__construct()' => [$params['aliases']],
    ],
];
