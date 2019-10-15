<?php

use App\Console\Command\CreateUser;
use App\Console\Command\MigrateDown;
use App\Console\Command\MigrateGenerate;
use App\Console\Command\MigrateList;
use App\Console\Command\MigrateUp;

return [
    'mailer.host' => 'smtp.example.com',
    'mailer.port' => 25,
    'mailer.encryption' => null,
    'mailer.username' => 'admin@example.com',
    'mailer.password' => '',

    'supportEmail' => 'support@example.com',

    'commands' => [
        'user/create'      => CreateUser::class,
        'migrate/generate' => MigrateGenerate::class,
        'migrate/up'       => MigrateUp::class,
        'migrate/down'     => MigrateDown::class,
        'migrate/list'     => MigrateList::class,
    ],
];
