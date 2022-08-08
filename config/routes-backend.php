<?php

declare(strict_types=1);

use Yiisoft\Router\Route;

return [
    Yiisoft\Router\Group::create('')
        ->routes(
            Route::get('/')
                ->action([App\Http\Controllers\Backend\SiteController::class, 'index'])
                ->name('index'),
        )
        ->host('backend.{_host}')
        ->namePrefix('backend/'),

    Route::get('/backend')
        ->action([App\Http\Controllers\Backend\SiteController::class, 'index'])
        ->name('index'),
];
