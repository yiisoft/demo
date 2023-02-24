<?php

declare(strict_types=1);

use App\Infrastructure\IO\Http;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsHtml;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsJson;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Swagger\Middleware\SwaggerJson;
use Yiisoft\Swagger\Middleware\SwaggerUi;

return [
    Route::get('/')
        ->action([Http\Home\GetIndex\Action::class, '__invoke'])
        ->name('api/info'),

    Route::get('/blog/')
        ->action([Http\Blog\GetIndex\Action::class, '__invoke'])
        ->name('blog/index'),

    Route::get('/blog/{id:\d+}')
        ->action([Http\Blog\GetView\Action::class, '__invoke'])
        ->name('blog/view'),

    Route::post('/blog/')
        ->middleware(Authentication::class)
        ->action([Http\Blog\PostCreate\Action::class, '__invoke'])
        ->name('blog/create'),

    Route::put('/blog/{id:\d+}')
        ->middleware(Authentication::class)
        ->action([Http\Blog\PutUpdate\Action::class, '__invoke'])
        ->name('blog/update'),

    Route::get('/users/')
        ->middleware(Authentication::class)
        ->action([Http\User\GetIndex\Action::class, '__invoke'])
        ->name('users/index'),

    Route::get('/users/{id:\d+}')
        ->middleware(Authentication::class)
        ->action([Http\User\GetView\Action::class, '__invoke'])
        ->name('users/view'),

    Route::post('/auth/login')
        ->action([Http\Auth\PostLogin\Action::class, '__invoke'])
        ->name('auth'),

    Route::post('/auth/logout')
        ->middleware(Authentication::class)
        ->action([Http\Auth\PostLogout\Action::class, '__invoke'])
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
                ->action(SwaggerJson::class),
        ),
];
