<?php

declare(strict_types=1);

use App\Auth\AuthController;
use App\Blog\BlogController;
use App\Factory\RestGroupFactory;
use App\InfoController;
use App\User\UserController;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsHtml;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsJson;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Swagger\Middleware\SwaggerJson;
use Yiisoft\Swagger\Middleware\SwaggerUi;

return [
    Route::get('/')
        ->action([\App\Controller\HomeController::class, 'index'])
        ->name('home'),

    Route::get('/simple/{name:\w+}')
        ->action([\App\Controller\WorkflowController::class, 'simpleAction'])
        ->name('temporal/simple'),

    Route::get('/complicated/{name:\w+}')
        ->action([\App\Controller\WorkflowController::class, 'complicatedAction'])
        ->name('temporal/complicated'),

    Route::get('/asynchronous/{name:\w+}')
        ->action([\App\Controller\WorkflowController::class, 'asynchronousAction'])
        ->name('temporal/asynchronous'),

    Route::get('/asynchronous-status/{id:[\w-]+}')
        ->action([\App\Controller\WorkflowController::class, 'asynchronousStatusAction'])
        ->name('temporal/asynchronous-status'),

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
