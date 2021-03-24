<?php

declare(strict_types=1);

use Yiisoft\Yii\Console\Command\Serve;

return [
    'yiisoft/yii-console' => [
        'id' => 'yii-console',
        'name' => 'Yii Console',
        'autoExit' => false,
        'commands' => [
            'serve' => Serve::class,
            'user/create' => App\User\Console\CreateCommand::class,
            'user/assignRole' => App\User\Console\AssignRoleCommand::class,
            'fixture/add' => App\Command\Fixture\AddCommand::class,
            'router/list' => App\Command\Router\ListCommand::class,
        ],
        'version' => '3.0',
    ],
];
