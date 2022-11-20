<?php

declare(strict_types=1);

use App\Auth\AuthController;
use App\Blog\BlogController;
use App\InfoController;
use App\User\UserController;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsHtml;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsJson;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Swagger\Middleware\SwaggerJson;
use Yiisoft\Swagger\Middleware\SwaggerUi;
use App\Factory\RestGroupFactory;

return [
    Route::get('/')
        ->action([InfoController::class, 'index'])
        ->name('api/info'),

    Route::get('/blog/')
        ->action([BlogController::class, 'index'])
        ->name('blog/index'),

    Route::get('/blog/{id:\d+}')
        ->action([BlogController::class, 'view'])
        ->name('blog/view'),

    Route::post('/blog/')
        ->middleware(Authentication::class)
        ->action([BlogController::class, 'create'])
        ->name('blog/create'),

    Route::put('/blog/{id:\d+}')
        ->middleware(Authentication::class)
        ->action([BlogController::class, 'update'])
        ->name('blog/update'),

    RestGroupFactory::create('/users/', UserController::class)
        ->prependMiddleware(Authentication::class),

    Route::post('/auth/')
        ->action([AuthController::class, 'login'])
        ->name('auth'),

    Route::post('/logout/')
        ->middleware(Authentication::class)
        ->action([AuthController::class, 'logout'])
        ->name('logout'),

    // Swagger routes
    Group::create('/docs')
        ->routes(
            Route::get('')
                ->middleware(FormatDataResponseAsHtml::class)
                ->action(fn (SwaggerUi $swaggerUi) => $swaggerUi->withJsonUrl('/docs/openapi.json')),
            Route::get('/openapi.json')
                ->middleware(FormatDataResponseAsJson::class)
                ->action(SwaggerJson::class),
        ),
];
