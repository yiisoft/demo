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
use App\Middleware\ActionCaller;
use App\Middleware\SetFormat;
use Psr\Container\ContainerInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\FastRoute\UrlMatcher;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Router\RouteCollectorInterface;

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
                Group::create('/archive', [
                    // Index page
                    Group::create('', [
                        Route::get('', [new ActionCaller(ArchiveController::class, 'index', $container), 'process'])
                            ->addMiddleware([new SetFormat('html', null), 'process'])
                            ->name('blog/archive/index'),
                        Route::get('/print_r', [new ActionCaller(ArchiveController::class, 'index', $container), 'process'])
                            ->addMiddleware([new SetFormat('plain'), 'process'])
                            ->name('blog/archive/index/print_r'),
                        Route::get('/xml', [new ActionCaller(ArchiveController::class, 'index', $container), 'process'])
                            ->addMiddleware([new SetFormat('xml'), 'process'])
                            ->name('blog/archive/index/xml'),
                        Route::get('/json', [new ActionCaller(ArchiveController::class, 'index', $container), 'process'])
                            ->name('blog/archive/index/json'),
                        Route::get('/custom', [new ActionCaller(ArchiveController::class, 'custom', $container), 'process'])
                            ->name('blog/archive/index/custom'),
                    ]),
                    // Yearly page
                    Route::get('/{year:\d+}', [ArchiveController::class, 'yearlyArchive'])
                        ->name('blog/archive/year'),
                    // Monthly page
                    Route::get('/{year:\d+}-{month:\d+}[/page{page:\d+}]', [ArchiveController::class, 'monthlyArchive'])
                        ->name('blog/archive/month')
                ])->addMiddleware([new SetFormat('json'), 'process']),
            ]),
        ];

        $collector =  $container->get(RouteCollectorInterface::class);
        $collector->addGroup(Group::create(null, $routes));

        return new UrlMatcher(new RouteCollection($collector));
    }
}
