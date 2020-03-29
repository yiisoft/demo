<?php

namespace App\Factory;

use App\Blog\Archive\ArchiveController;
use App\Blog\BlogController;
use App\Blog\Post\PostController;
use App\Blog\Tag\TagController;
use App\Controller\ApiInfo;
use App\Controller\ApiUserController;
use App\Controller\AuthController;
use App\Controller\ContactController;
use App\Controller\SiteController;
use App\Controller\UserController;
use App\JsonResponseFormatter;
use App\Response;
use App\ResponseFactory;
use App\ResponseFormatter;
use App\DeferredResponseFormatter;
use App\XmlResponseFormatter;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
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

            // User
            Group::create('/api', [
                Route::get('/info/v1', function (ResponseFactory $responseFactory) {
                    return $responseFactory->createResponse(200, '', ['version' => '1.0', 'author' => 'yiiliveext']);
                }),
                Route::get('/info/v2', ApiInfo::class)
                    ->addMiddleware(new ResponseFormatter($container->get(JsonResponseFormatter::class))),
                Route::get('/user', [ApiUserController::class, 'index'])
                    ->name('api/user/index'),
                Route::get('/user/{login}', [ApiUserController::class, 'profile'])
                    ->addMiddleware(new DeferredResponseFormatter($container->get(JsonResponseFormatter::class)))
                    ->name('api/user/profile'),
            ], $container)->addMiddleware(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
                $response = $handler->handle($request);
                if ($response instanceof Response) {
                    $data = $response->getData();
                    if ($response->getStatusCode() !== 200) {
                        if (!empty($data)) {
                            $message = $data;
                        } else {
                            $message = 'Unknown error';
                        }
                        return $response->withData(
                            ['status' => 'failed',
                                'error' => ['message' => $message, 'status' => $response->getStatusCode()]]);
                    }
                    return $response->withData(['status' => 'success', 'data' => $data]);
                }

                return $response;
            })->addMiddleware(new DeferredResponseFormatter($container->get(XmlResponseFormatter::class))),

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

        $collector =  $container->get(RouteCollectorInterface::class);
        $collector->addGroup(
            Group::create(null, $routes)
                ->addMiddleware($container->get(DeferredResponseFormatter::class))
        );

        return new UrlMatcher(new RouteCollection($collector));
    }
}
