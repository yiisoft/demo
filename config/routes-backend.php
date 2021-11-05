<?php

declare(strict_types=1);

use Yiisoft\Router\Route;

return [
    \Yiisoft\Router\Group::create('')
        ->routes(
            Route::get('/')
                ->action([\App\Backend\Controller\SiteController::class, 'index'])
                ->name('index'),
        )
        ->host('backend.{_host}')
        ->namePrefix('backend/'),
];
