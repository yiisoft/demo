<?php

declare(strict_types=1);

use Yiisoft\Yii\Console\Command\Serve;

return [
    'serve' => Serve::class,
    'user/create' => App\User\Console\CreateCommand::class,
    'user/assignRole' => App\User\Console\AssignRoleCommand::class,
    'fixture/add' => App\Command\Fixture\AddCommand::class,
    'fixture/schema/clear' => App\Command\Fixture\SchemaClearCommand::class,
    'router/list' => App\Command\Router\ListCommand::class,
    'translator/translate' => App\Command\Translation\TranslateCommand::class,
];
