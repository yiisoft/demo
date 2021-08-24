<?php

declare(strict_types=1);

use Yiisoft\Router\Route;

return [
    Route::get('/app')
        ->action([\App\Backend\Controllers\SiteController::class, 'index'])
        ->name('backend/index')
        ->host('backend.{_host}'),
];
