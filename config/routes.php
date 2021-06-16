<?php

declare(strict_types=1);

use App\Blog\Archive\ArchiveController;
use App\Blog\BlogController;
use App\Blog\CommentController;
use App\Blog\Post\PostController;
use App\Blog\Tag\TagController;
use App\Contact\ContactController;
use App\Invoice\InvoiceController;
use App\Invoice\Client\ClientController;
use App\Invoice\Setting\SettingController;
use App\Invoice\EmailTemplate\EmailTemplateController;
use App\Invoice\Family\FamilyController;
use App\Invoice\TaxRate\TaxRateController;
use App\Invoice\Unit\UnitController;
use App\Invoice\Product\ProductController;
use App\Controller\ApiInfo;
use App\Controller\AuthController;
use App\Controller\SignupController;
use App\Controller\SiteController;
use App\User\Controller\ApiUserController;
use App\Middleware\AccessChecker;
use App\Middleware\ApiDataWrapper;
use App\User\Controller\UserController;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsHtml;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsJson;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsXml;
use Yiisoft\Http\Method;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Session\SessionMiddleware;
use Yiisoft\Swagger\Middleware\SwaggerJson;
use Yiisoft\Swagger\Middleware\SwaggerUi;

return [
    // Lonely pages of site
    Route::get('/')
        ->action([SiteController::class, 'index'])
        ->name('site/index'),
    Route::methods([Method::GET, Method::POST], '/contact')
        ->action([ContactController::class, 'contact'])
        ->name('site/contact'),
    Route::methods([Method::GET, Method::POST], '/login')
        ->action([AuthController::class, 'login'])
        ->name('site/login'),
    Route::post('/logout')
        ->action([AuthController::class, 'logout'])
        ->name('site/logout'),
    Route::methods([Method::GET, Method::POST], '/signup')
        ->action([SignupController::class, 'signup'])
        ->name('site/signup'),

    // User
    Group::create('/user')
        ->routes(
        // Index
            Route::get('[/page-{page:\d+}]')
                ->action([UserController::class, 'index'])
                ->name('user/index'),
            // Profile page
            Route::get('/{login}')
                ->action([UserController::class, 'profile'])
                ->name('user/profile')
        ),

    // API group.
    // By default it responds with XML regardless of content-type.
    // Individual sub-routes are responding with JSON.
    Group::create('/api')
        ->middleware(FormatDataResponseAsXml::class)
        ->middleware(ApiDataWrapper::class)
        ->routes(
            Route::get('/info/v1')
                ->name('api/info/v1')
                ->action(function (DataResponseFactoryInterface $responseFactory) {
                    return $responseFactory->createResponse(['version' => '1.0', 'author' => 'yiisoft']);
                }),
            Route::get('/info/v2')
                ->name('api/info/v2')
                ->middleware(FormatDataResponseAsJson::class)
                ->action(ApiInfo::class),
            Route::get('/user')
                ->name('api/user/index')
                ->action([ApiUserController::class, 'index']),
            Route::get('/user/{login}')
                ->name('api/user/profile')
                ->middleware(FormatDataResponseAsJson::class)
                ->action([ApiUserController::class, 'profile'])
        ),

    // Blog routes
    Group::create('/blog')
        ->routes(
        // Index
            Route::get('[/page{page:\d+}]')
                ->action([BlogController::class, 'index'])
                ->name('blog/index'),
            // Add Post page
            Route::methods([Method::GET, Method::POST], '/page/add')
                ->middleware(Authentication::class)
                ->action([PostController::class, 'add'])
                ->name('blog/add'),
            // Edit Post page
            Route::methods([Method::GET, Method::POST], '/page/edit/{slug}')
                ->name('blog/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPost'))
                ->middleware(Authentication::class)
                ->action([PostController::class, 'edit']),

            // Post page
            Route::get('/page/{slug}')
                ->action([PostController::class, 'index'])
                ->name('blog/post'),
            // Tag page
            Route::get('/tag/{label}[/page{page:\d+}]')
                ->action([TagController::class, 'index'])
                ->name('blog/tag'),
            // Archive
            Group::create('/archive')
                ->routes(
                // Index page
                    Route::get('')
                        ->action([ArchiveController::class, 'index'])
                        ->name('blog/archive/index'),
                    // Yearly page
                    Route::get('/{year:\d+}')
                        ->action([ArchiveController::class, 'yearlyArchive'])
                        ->name('blog/archive/year'),
                    // Monthly page
                    Route::get('/{year:\d+}-{month:\d+}[/page{page:\d+}]')
                        ->action([ArchiveController::class, 'monthlyArchive'])
                        ->name('blog/archive/month'),
                ),
            // comments
            Route::get('/comments/[next/{next}]')
                ->action([CommentController::class, 'index'])
                ->name('blog/comment/index')
    ),                    

    // Swagger routes
    Group::create('/swagger')
        ->routes(
            Route::get('')
                ->middleware(FormatDataResponseAsHtml::class)
                ->action(fn (SwaggerUi $swaggerUi) => $swaggerUi->withJsonUrl('/swagger/json-url'))
                ->name('swagger/index'),
            Route::get('/json-url')
                ->middleware(FormatDataResponseAsJson::class)
                ->action(static function (SwaggerJson $swaggerJson) {
                    return $swaggerJson
                        // Uncomment cache for production environment
                        // ->withCache(60)
                        ->withAnnotationPaths([
                            '@src/Controller', // Path to API controllers
                        ]);
                }),
        ),
    
    // Invoice routes
    Group::create('/invoice')
        ->routes(
        // Index
            //so nothing will appear over the tooltip when you hover over the button ie. ''                
            Route::get('')
                //add session just for Invoice
                ->middleware(SessionMiddleware::class)
                ->action([InvoiceController::class, 'index'])
                ->name('invoice/index'),
            //so if you add the below /client to the above '/invoice' you get '/invoice/client', does that look familiar?
            Route::get('/client')
                ->action([ClientController::class, 'index'])
                ->name('client/index'),    
            // Add Client
            Route::methods([Method::GET, Method::POST], '/client/add')
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'add'])
                ->name('client/add'),
            // Edit Client
            Route::methods([Method::GET, Method::POST], '/client/edit/{client_id}')
                ->name('client/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/client/delete/{client_id}')
                ->name('client/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/client/view/{client_id}')
                ->name('client/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'view']),
                
            Route::get('/setting')
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'index'])
                ->name('setting/index'),    
            // Add Setting
            Route::methods([Method::GET, Method::POST], '/setting/add')
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'add'])
                ->name('setting/add'),
            // Edit Setting
            Route::methods([Method::GET, Method::POST], '/setting/edit/{setting_id}')
                ->name('setting/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editSetting'))
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/setting/delete/{setting_id}')
                ->name('setting/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editSetting'))
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/setting/view/{setting_id}')
                ->name('setting/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editSetting'))
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'view']),    
            
            Route::get('/emailtemplate')
                ->middleware(Authentication::class)
                ->action([EmailTemplateController::class, 'index'])
                ->name('emailtemplate/index'),    
            // Add EmailTemplate
            Route::methods([Method::GET, Method::POST], '/emailtemplate/add')
                ->middleware(Authentication::class)
                ->action([EmailTemplateController::class, 'add'])
                ->name('emailtemplate/add'),
            // Edit EmailTemplate
            Route::methods([Method::GET, Method::POST], '/emailtemplate/edit/{email_template_id}')
                ->name('emailtemplate/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editEmailTemplate'))
                ->middleware(Authentication::class)
                ->action([EmailTemplateController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/emailtemplate/delete/{email_template_id}')
                ->name('emailtemplate/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editEmailTemplate'))
                ->middleware(Authentication::class)
                ->action([EmailTemplateController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/emailtemplate/view/{email_template_id}')
                ->name('emailtemplate/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editEmailTemplate'))
                ->middleware(Authentication::class)
                ->action([EmailTemplateController::class, 'view']),    
            
            Route::get('/family')
                ->middleware(Authentication::class)
                ->action([FamilyController::class, 'index'])
                ->name('family/index'),    
            // Add Family
            Route::methods([Method::GET, Method::POST], '/family/add')
                ->middleware(Authentication::class)
                ->action([FamilyController::class, 'add'])
                ->name('family/add'),
            // Edit Family
            Route::methods([Method::GET, Method::POST], '/family/edit/{family_id}')
                ->name('family/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editFamily'))
                ->middleware(Authentication::class)
                ->action([FamilyController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/family/delete/{family_id}')
                ->name('family/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editFamily'))
                ->middleware(Authentication::class)
                ->action([FamilyController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/family/view/{family_id}')
                ->name('family/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editFamily'))
                ->middleware(Authentication::class)
                ->action([FamilyController::class, 'view']),
                
            Route::get('/taxrate')
                ->middleware(Authentication::class)
                ->action([TaxRateController::class, 'index'])
                ->name('taxrate/index'),    
            // Add TaxRate
            Route::methods([Method::GET, Method::POST], '/taxrate/add')
                ->middleware(Authentication::class)
                ->action([TaxRateController::class, 'add'])
                ->name('taxrate/add'),
            // Edit TaxRate
            Route::methods([Method::GET, Method::POST], '/taxrate/edit/{tax_rate_id}')
                ->name('taxrate/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editTaxrate'))
                ->middleware(Authentication::class)
                ->action([TaxRateController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/taxrate/delete/{tax_rate_id}')
                ->name('taxrate/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editTaxrate'))
                ->middleware(Authentication::class)
                ->action([TaxRateController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/taxrate/view/{tax_rate_id}')
                ->name('taxrate/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editTaxrate'))
                ->middleware(Authentication::class)
                ->action([TaxRateController::class, 'view']),
                
            Route::get('/unit')
                ->middleware(Authentication::class)
                ->action([UnitController::class, 'index'])
                ->name('unit/index'),    
            // Add Unit
            Route::methods([Method::GET, Method::POST], '/unit/add')
                ->middleware(Authentication::class)
                ->action([UnitController::class, 'add'])
                ->name('unit/add'),
            // Edit Unit
            Route::methods([Method::GET, Method::POST], '/unit/edit/{unit_id}')
                ->name('unit/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editUnit'))
                ->middleware(Authentication::class)
                ->action([UnitController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/unit/delete/{unit_id}')
                ->name('unit/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editUnit'))
                ->middleware(Authentication::class)
                ->action([UnitController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/unit/view/{unit_id}')
                ->name('unit/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editUnit'))
                ->middleware(Authentication::class)
                ->action([UnitController::class, 'view']),
            
            Route::get('/product')
                ->middleware(Authentication::class)
                ->action([ProductController::class, 'index'])
                ->name('product/index'),    
            // Add Product
            Route::methods([Method::GET, Method::POST], '/product/add')
                ->middleware(Authentication::class)
                ->action([ProductController::class, 'add'])
                ->name('product/add'),
            // Edit Product
            Route::methods([Method::GET, Method::POST], '/product/edit/{product_id}')
                ->name('product/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProduct'))
                ->middleware(Authentication::class)
                ->action([ProductController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/product/delete/{product_id}')
                ->name('product/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProduct'))
                ->middleware(Authentication::class)
                ->action([ProductController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/product/view/{product_id}')
                ->name('product/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProduct'))
                ->middleware(Authentication::class)
                ->action([ProductController::class, 'view']),    
        ),//invoice          
];
