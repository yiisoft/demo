<?php

declare(strict_types=1);

return [
    'mailer' => [
        'adminEmail' => 'admin@example.com',
        'senderEmail' => 'sender@example.com',
    ],

    'yiisoft/aliases' => [
        'aliases' => [
            '@root' => dirname(__DIR__),
            '@assets' => '@root/public/assets',
            '@assetsUrl' => '/assets',
            '@baseUrl' => '/',
            '@messages' => '@resources/messages',
            '@npm' => '@root/node_modules',
            '@public' => '@root/public',
            '@resources' => '@root/resources',
            '@runtime' => '@root/runtime',
            '@src' => '@root/src',
            '@vendor' => '@root/vendor',
            '@layout' => '@root/views/layout',
            '@views' => '@root/views',
        ],
    ],
];
