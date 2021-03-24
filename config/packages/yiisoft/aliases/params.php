<?php

declare(strict_types=1);

return [
    'yiisoft/aliases' => [
        'aliases' => [
            '@root' => dirname(__DIR__, 4),
            '@views' => '@root/views',
            '@resources' => '@root/resources',
            '@src' => '@root/src',
            '@assets' => '@public/assets',
            '@assetsUrl' => '@baseUrl/assets',
        ],
    ],
];
