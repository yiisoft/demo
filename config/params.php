<?php

use App\ConsoleCommand\CreateUser;

return [
    'mailer.host' => 'smtp.example.com',
    'mailer.port' => 25,
    'mailer.encryption' => null,
    'mailer.username' => 'admin@example.com',
    'mailer.password' => '',

    'supportEmail' => 'support@example.com',

    'commands' => [
        'user/create' => CreateUser::class,
    ],
];
