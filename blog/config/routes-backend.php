<?php

declare(strict_types=1);

use App\Backend\Controller\SiteController;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;

return [
    Group::create('')
        ->routes(
            Route::get('/')
                ->action([SiteController::class, 'index'])
                ->name('index'),
        )
        ->host('backend.{_host}')
        ->namePrefix('backend/'),

    Route::get('/backend')
        ->action([SiteController::class, 'index'])
        ->name('index'),
];
