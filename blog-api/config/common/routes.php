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
use Yiisoft\RequestProvider\RequestCatcherMiddleware;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Swagger\Action\SwaggerJson;
use Yiisoft\Swagger\Middleware\SwaggerUi;
use Yiisoft\Yii\Middleware\CorsAllowAll;

return [
    Route::get('/')
        ->action([InfoController::class, 'index'])
        ->name('api/info'),

    Route::get('/blog/')
        ->middleware(RequestCatcherMiddleware::class)
        ->action([BlogController::class, 'index'])
        ->name('blog/index'),

    Route::get('/blog/{id:\d+}')
        ->action([BlogController::class, 'view'])
        ->name('blog/view'),

    Route::post('/blog/')
        ->middleware(Authentication::class)
        ->middleware(RequestCatcherMiddleware::class)
        ->action([BlogController::class, 'create'])
        ->name('blog/create'),

    Route::put('/blog/{id:\d+}')
        ->middleware(Authentication::class)
        ->middleware(RequestCatcherMiddleware::class)
        ->action([BlogController::class, 'update'])
        ->name('blog/update'),

    RestGroupFactory::create('/users/', UserController::class)
        ->prependMiddleware(Authentication::class),

    Route::post('/auth/')
        ->middleware(RequestCatcherMiddleware::class)
        ->action([AuthController::class, 'login'])
        ->name('auth'),

    Route::post('/logout/')
        ->middleware(Authentication::class)
        ->middleware(RequestCatcherMiddleware::class)
        ->action([AuthController::class, 'logout'])
        ->name('logout'),

    // Swagger routes
    Group::create('/docs')
        ->routes(
            Route::get('')
                ->middleware(FormatDataResponseAsHtml::class)
                ->action(function (SwaggerUi $swaggerUi, UrlGeneratorInterface $urlGenerator) {
                    return $swaggerUi->withJsonUrl($urlGenerator->getUriPrefix() . '/docs/openapi.json');
                }),
            Route::get('/openapi.json')
                ->middleware(FormatDataResponseAsJson::class)
                ->middleware(CorsAllowAll::class)
                ->action([SwaggerJson::class, 'process']),
        ),
];
