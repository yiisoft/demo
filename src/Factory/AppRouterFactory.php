<?php

namespace App\Factory;

use App\Blog\Archive\ArchiveController;
use App\Blog\BlogController;
use App\Blog\Post\PostController;
use App\Blog\Tag\TagController;
use App\Controller\AuthController;
use App\Controller\ContactController;
use App\Controller\SiteController;
use App\Controller\UserController;
use Psr\Container\ContainerInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\FastRoute\FastRouteFactory;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouterFactory;

class AppRouterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $routes = [
            // Lonely pages of site
            Route::get('/', [SiteController::class, 'index'])
                ->name('site/index'),
            Route::methods([Method::GET, Method::POST], '/contact', [ContactController::class, 'contact'])
                 ->name('site/contact'),
            Route::methods([Method::GET, Method::POST], '/login', [AuthController::class, 'login'])
                 ->name('site/login'),
            Route::get('/logout', [AuthController::class, 'logout'])
                 ->name('site/logout'),

            // User
            Group::create('/user', [
                // Index
                Route::get('[/page-{page:\d+}]', [UserController::class, 'index'])
                     ->name('user/index'),
                // Profile page
                Route::get('/{login}', [UserController::class, 'profile'])
                     ->name('user/profile'),
            ]),

            // Blog routes
            Group::create('/blog', [
                // Index
                Route::get('[/page{page:\d+}]', [BlogController::class, 'index'])
                     ->name('blog/index'),
                // Post page
                Route::get('/page/{slug}', [PostController::class, 'index'])
                     ->name('blog/post'),
                // Tag page
                Route::get('/tag/{label}[/page{page:\d+}]', [TagController::class, 'index'])
                     ->name('blog/tag'),
                // Archive
                Group::create('/blog', [
                    // Index page
                    Route::get('', [ArchiveController::class, 'index'])
                         ->name('blog/archive/index'),
                    // Yearly page
                    Route::get('/{year:\d+}', [ArchiveController::class, 'yearlyArchive'])
                         ->name('blog/archive/year'),
                    // Monthly page
                    Route::get('/{year:\d+}-{month:\d+}[/page{page:\d+}]', [ArchiveController::class, 'monthlyArchive'])
                         ->name('blog/archive/month')
                ]),
            ]),
        ];

        return (new RouterFactory(new FastRouteFactory(), $routes))($container);
    }
}
