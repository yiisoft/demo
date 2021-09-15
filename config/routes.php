<?php

declare(strict_types=1);

use App\Blog\Archive\ArchiveController;
use App\Blog\BlogController;
use App\Blog\CommentController;
use App\Blog\Post\PostController;
use App\Blog\Tag\TagController;
use App\Contact\ContactController;
use App\Controller\ApiInfo;
use App\Controller\AuthController;
use App\Controller\SignupController;
use App\Controller\SiteController;
use App\Middleware\AccessChecker;
use App\Middleware\ApiDataWrapper;
use App\User\Controller\ApiUserController;
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

use App\Invoice\InvoiceController;
use App\Invoice\Client\ClientController;
use App\Invoice\Setting\SettingController;
use App\Invoice\EmailTemplate\EmailTemplateController;
use App\Invoice\Family\FamilyController;
use App\Invoice\TaxRate\TaxRateController;
use App\Invoice\Unit\UnitController;
use App\Invoice\Product\ProductController;
use App\Invoice\Task\TaskController;
use App\Invoice\Project\ProjectController;
use App\Invoice\Group\GroupController;
use App\Invoice\Inv\InvController;
use App\Invoice\Recurring\RecurringController;
use App\Invoice\InvAmount\InvAmountController;
use App\Invoice\InvItem\InvItemController;
use App\Invoice\InvTaxRate\InvTaxRateController;
use App\Invoice\Invcust\InvcustController;
use App\Invoice\ItemLookup\ItemLookupController;
use App\Invoice\Sumex\SumexController;
use App\Invoice\Merchant\MerchantController;
use App\Invoice\Import\ImportController;
use App\Invoice\CustomField\CustomFieldController;
use App\Invoice\CustomValue\CustomValueController;
use App\Invoice\ClientCustom\ClientCustomController;
use App\Invoice\ClientNote\ClientNoteController;
use App\Invoice\Quote\QuoteController;
use App\Invoice\QuoteItem\QuoteItemController;
use App\Invoice\QuoteItemAmount\QuoteItemAmountController;
use App\Invoice\QuoteAmount\QuoteAmountController;
use App\Invoice\QuoteTaxRate\QuoteTaxRateController;
use App\Invoice\Payment\PaymentController;
use App\Invoice\PaymentMethod\PaymentMethodController;
use App\Invoice\PaymentCustom\PaymentCustomController;
use App\Invoice\Generator\GeneratorController;
use App\Invoice\GeneratorRelation\GeneratorRelationController;

return [
    // Lonely pages of site
    Route::get('/')
        ->action([SiteController::class, 'index'])
        ->name('site/index'),
    Route::post('/locale')
        ->action([SiteController::class, 'setLocale'])
        ->name('site/set-locale'),
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
            Route::get('/client/[page{page:\d+}]')
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'index'])
                ->name('client/index'),
            // Add Client
            Route::methods([Method::GET, Method::POST], '/client/add')
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'add'])
                ->name('client/add'),
            // Edit Client
            Route::methods([Method::GET, Method::POST], '/client/edit/{id}')
                ->name('client/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/client/delete/{id}')
                ->name('client/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/client/view/{id}')
                ->name('client/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'view']),
                
            Route::get('/generatorrelation')
                ->middleware(Authentication::class)
                ->action([GeneratorRelationController::class, 'index'])
                ->name('generatorrelation/index'),    
            // Add GeneratorRelation
            Route::methods([Method::GET, Method::POST], '/generatorrelation/add')
                ->middleware(Authentication::class)
                ->action([GeneratorRelationController::class, 'add'])
                ->name('generatorrelation/add'),
            // Edit GeneratorRelation
            Route::methods([Method::GET, Method::POST], '/generatorrelation/edit/{id}')
                ->name('generatorrelation/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGeneratorRelation'))
                ->middleware(Authentication::class)
                ->action([GeneratorRelationController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/generatorrelation/delete/{id}')
                ->name('generatorrelation/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGeneratorRelation'))
                ->middleware(Authentication::class)
                ->action([GeneratorRelationController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/generatorrelation/view/{id}')
                ->name('generatorrelation/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorRelationController::class, 'view']),
                
            Route::get('/generator')
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'index'])
                ->name('generator/index'),    
            // Add Generator
            Route::methods([Method::GET, Method::POST], '/generator/add')
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'add'])
                ->name('generator/add'),
            // Edit Generator
            Route::methods([Method::GET, Method::POST], '/generator/edit/{id}')
                ->name('generator/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/generator/delete/{id}')
                ->name('generator/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/generator/view/{id}')
                ->name('generator/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'view']),
            Route::methods([Method::GET, Method::POST], '/generator/entity/{id}')
                ->name('generator/entity')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'entity']),
            Route::methods([Method::GET, Method::POST], '/generator/repo/{id}')
                ->name('generator/repo')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'repo']),    
            Route::methods([Method::GET, Method::POST], '/generator/service/{id}')
                ->name('generator/service')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'service']),  
            Route::methods([Method::GET, Method::POST], '/generator/mapper/{id}')
                ->name('generator/mapper')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'mapper']),      
            Route::methods([Method::GET, Method::POST], '/generator/controller/{id}')
                ->name('generator/controller')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'controller']),          
            Route::methods([Method::GET, Method::POST], '/generator/form/{id}')
                ->name('generator/form')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'form']),
            Route::methods([Method::GET, Method::POST], '/generator/scope/{id}')
                ->name('generator/scope')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'scope']),
            Route::methods([Method::GET, Method::POST], '/generator/_index/{id}')
                ->name('generator/_index')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, '_index']),
             Route::methods([Method::GET, Method::POST], '/generator/_index_adv_paginator/{id}')
                ->name('generator/_index_adv_paginator')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, '_index_adv_paginator']),    
            Route::methods([Method::GET, Method::POST], '/generator/_form/{id}')
                ->name('generator/_form')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, '_form']),
            Route::methods([Method::GET, Method::POST], '/generator/_view/{id}')
                ->name('generator/_view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, '_view']),
            Route::methods([Method::GET, Method::POST], '/generator/_route/{id}')
                ->name('generator/_route')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, '_route']),
            Route::methods([Method::GET, Method::POST], '/generator/_form_modal_field/{id}')
                ->name('generator/_form_modal_field')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, '_form_modal_field']),
            Route::methods([Method::GET, Method::POST], '/generator/_form_modal_create/{id}')
                ->name('generator/_form_modal_create')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, '_form_modal_create']), 
                
            Route::get('/setting/[page{page:\d+}]')
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
            
            Route::get('/product/[page{page:\d+}]')
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
            
            Route::get('/project')
                ->middleware(Authentication::class)
                ->action([ProjectController::class, 'index'])
                ->name('project/index'),    
            // Add Project
            Route::methods([Method::GET, Method::POST], '/project/add')
                ->middleware(Authentication::class)
                ->action([ProjectController::class, 'add'])
                ->name('project/add'),
            // Edit Project
            Route::methods([Method::GET, Method::POST], '/project/edit/{id}')
                ->name('project/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProject'))
                ->middleware(Authentication::class)
                ->action([ProjectController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/project/delete/{id}')
                ->name('project/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProject'))
                ->middleware(Authentication::class)
                ->action([ProjectController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/project/view/{id}')
                ->name('project/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProject'))
                ->middleware(Authentication::class)
                ->action([ProjectController::class, 'view']),    
                
            Route::get('/task')
                ->middleware(Authentication::class)
                ->action([TaskController::class, 'index'])
                ->name('task/index'),    
            // Add Task
            Route::methods([Method::GET, Method::POST], '/task/add')
                ->middleware(Authentication::class)
                ->action([TaskController::class, 'add'])
                ->name('task/add'),
            // Edit Task
            Route::methods([Method::GET, Method::POST], '/task/edit/{id}')
                ->name('task/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editTask'))
                ->middleware(Authentication::class)
                ->action([TaskController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/task/delete/{id}')
                ->name('task/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editTask'))
                ->middleware(Authentication::class)
                ->action([TaskController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/task/view/{id}')
                ->name('task/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editTask'))
                ->middleware(Authentication::class)
                ->action([TaskController::class, 'view']),
            
            Route::get('/group')
                ->middleware(Authentication::class)
                ->action([GroupController::class, 'index'])
                ->name('group/index'),    
            // Add Group
            Route::methods([Method::GET, Method::POST], '/group/add')
                ->middleware(Authentication::class)
                ->action([GroupController::class, 'add'])
                ->name('group/add'),
            // Edit Group
            Route::methods([Method::GET, Method::POST], '/group/edit/{id}')
                ->name('group/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGroup'))
                ->middleware(Authentication::class)
                ->action([GroupController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/group/delete/{id}')
                ->name('group/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGroup'))
                ->middleware(Authentication::class)
                ->action([GroupController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/group/view/{id}')
                ->name('group/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGroup'))
                ->middleware(Authentication::class)
                ->action([GroupController::class, 'view']), 
            
            Route::get('/inv')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'index'])
                ->name('inv/index'),    
            // Add Inv
            Route::methods([Method::GET, Method::POST], '/inv/add')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'add'])
                ->name('inv/add'),
            // Edit Inv
            Route::methods([Method::GET, Method::POST], '/inv/edit/{id}')
                ->name('inv/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInv'))
                ->middleware(Authentication::class)
                ->action([InvController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/inv/delete/{id}')
                ->name('inv/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInv'))
                ->middleware(Authentication::class)
                ->action([InvController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/inv/view/{id}')
                ->name('inv/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInv'))
                ->middleware(Authentication::class)
                ->action([InvController::class, 'view']), 
            
            // Invoice Item    
            Route::get('/invitem')
                ->middleware(Authentication::class)
                ->action([InvItemController::class, 'index'])
                ->name('invitem/index'),    
            // Add Invoice Item
            Route::methods([Method::GET, Method::POST], '/invitem/add')
                ->middleware(Authentication::class)
                ->action([InvItemController::class, 'add'])
                ->name('invitem/add'),
            // Edit Invoice Item
            Route::methods([Method::GET, Method::POST], '/invitem/edit/{id}')
                ->name('invitem/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvItem'))
                ->middleware(Authentication::class)
                ->action([InvItemController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/invitem/delete/{id}')
                ->name('invitem/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvItem'))
                ->middleware(Authentication::class)
                ->action([InvItemController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/invitem/view/{id}')
                ->name('invitem/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvItem'))
                ->middleware(Authentication::class)
                ->action([InvItemController::class, 'view']), 
            
            //ItemLookup    
            Route::get('/itemlookup')
                ->middleware(Authentication::class)
                ->action([ItemLookupController::class, 'index'])
                ->name('itemlookup/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/itemlookup/add')
                ->middleware(Authentication::class)
                ->action([ItemLookupController::class, 'add'])
                ->name('itemlookup/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/itemlookup/edit/{id}')
                ->name('itemlookup/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editItemLookup'))
                ->middleware(Authentication::class)
                ->action([ItemLookupController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/itemlookup/delete/{id}')
                ->name('itemlookup/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editItemLookup'))
                ->middleware(Authentication::class)
                ->action([ItemLookupController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/itemlookup/view/{id}')
                ->name('itemlookup/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editItemLookup'))
                ->middleware(Authentication::class)
                ->action([ItemLookupController::class, 'view']),
                
            // Invoice Amount    
            Route::get('/invamount')
                ->middleware(Authentication::class)
                ->action([InvAmountController::class, 'index'])
                ->name('invamount/index'),    
            // Add Invoice Amount
            Route::methods([Method::GET, Method::POST], '/invamount/add')
                ->middleware(Authentication::class)
                ->action([InvAmountController::class, 'add'])
                ->name('invamount/add'),
            // Edit Invoice Amount
            Route::methods([Method::GET, Method::POST], '/invamount/edit/{id}')
                ->name('invamount/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvAmount'))
                ->middleware(Authentication::class)
                ->action([InvAmountController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/invamount/delete/{id}')
                ->name('invamount/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvAmount'))
                ->middleware(Authentication::class)
                ->action([InvAmountController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/invamount/view/{id}')
                ->name('invamount/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvAmount'))
                ->middleware(Authentication::class)
                ->action([InvAmountController::class, 'view']), 
                
            // Sumex    
            Route::get('/sumex')
                ->middleware(Authentication::class)
                ->action([SumexController::class, 'index'])
                ->name('sumex/index'),    
            // Add Sumex
            Route::methods([Method::GET, Method::POST], '/sumex/add')
                ->middleware(Authentication::class)
                ->action([SumexController::class, 'add'])
                ->name('sumex/add'),
            // Edit Sumex
            Route::methods([Method::GET, Method::POST], '/sumex/edit/{id}')
                ->name('sumex/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editSumex'))
                ->middleware(Authentication::class)
                ->action([SumexController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/sumex/delete/{id}')
                ->name('sumex/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editSumex'))
                ->middleware(Authentication::class)
                ->action([SumexController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/sumex/view/{id}')
                ->name('sumex/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editSumex'))
                ->middleware(Authentication::class)
                ->action([SumexController::class, 'view']), 
                
            // Merchant   
            Route::get('/merchant')
                ->middleware(Authentication::class)
                ->action([MerchantController::class, 'index'])
                ->name('merchant/index'),    
            // Add Merchant
            Route::methods([Method::GET, Method::POST], '/merchant/add')
                ->middleware(Authentication::class)
                ->action([MerchantController::class, 'add'])
                ->name('merchant/add'),
            // Edit Merchant
            Route::methods([Method::GET, Method::POST], '/merchant/edit/{id}')
                ->name('merchant/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editMerchant'))
                ->middleware(Authentication::class)
                ->action([MerchantController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/merchant/delete/{id}')
                ->name('merchant/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editMerchant'))
                ->middleware(Authentication::class)
                ->action([MerchantController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/merchant/view/{id}')
                ->name('merchant/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editMerchant'))
                ->middleware(Authentication::class)
                ->action([MerchantController::class, 'view']), 
                
            // Invoice Custom    
            Route::get('/invcust')
                ->middleware(Authentication::class)
                ->action([InvcustController::class, 'index'])
                ->name('invcust/index'),    
            // Add Invcust
            Route::methods([Method::GET, Method::POST], '/invcust/add')
                ->middleware(Authentication::class)
                ->action([InvcustController::class, 'add'])
                ->name('invcust/add'),
            // Edit Invcust
            Route::methods([Method::GET, Method::POST], '/invcust/edit/{id}')
                ->name('invcust/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvcust'))
                ->middleware(Authentication::class)
                ->action([InvcustController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/invcust/delete/{id}')
                ->name('invcust/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvcust'))
                ->middleware(Authentication::class)
                ->action([InvcustController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/invcust/view/{id}')
                ->name('invcust/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvcust'))
                ->middleware(Authentication::class)
                ->action([InvcustController::class, 'view']), 
                
            // Custom Field
            Route::get('/customfield')
                ->middleware(Authentication::class)
                ->action([CustomFieldController::class, 'index'])
                ->name('customfield/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/customfield/add')
                ->middleware(Authentication::class)
                ->action([CustomFieldController::class, 'add'])
                ->name('customfield/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/customfield/edit/{id}')
                ->name('customfield/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editCustomField'))
                ->middleware(Authentication::class)
                ->action([CustomFieldController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/customfield/delete/{id}')
                ->name('customfield/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editCustomField'))
                ->middleware(Authentication::class)
                ->action([CustomFieldController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/customfield/view/{id}')
                ->name('customfield/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editCustomField'))
                ->middleware(Authentication::class)
                ->action([CustomFieldController::class, 'view']),      
            
            // Custom Value
            Route::get('/customvalue')
                ->middleware(Authentication::class)
                ->action([CustomValueController::class, 'index'])
                ->name('customvalue/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/customvalue/add')
                ->middleware(Authentication::class)
                ->action([CustomValueController::class, 'add'])
                ->name('customvalue/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/customvalue/edit/{id}')
                ->name('customvalue/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editCustomValue'))
                ->middleware(Authentication::class)
                ->action([CustomValueController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/customvalue/delete/{id}')
                ->name('customvalue/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editCustomValue'))
                ->middleware(Authentication::class)
                ->action([CustomValueController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/customvalue/view/{id}')
                ->name('customvalue/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editCustomValue'))
                ->middleware(Authentication::class)
                ->action([CustomValueController::class, 'view']),          
                
            // Client Custom
            Route::get('/clientcustom')
                ->middleware(Authentication::class)
                ->action([ClientCustomController::class, 'index'])
                ->name('clientcustom/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/clientcustom/add')
                ->middleware(Authentication::class)
                ->action([ClientCustomController::class, 'add'])
                ->name('clientcustom/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/clientcustom/edit/{id}')
                ->name('clientcustom/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClientCustom'))
                ->middleware(Authentication::class)
                ->action([ClientCustomController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/clientcustom/delete/{id}')
                ->name('clientcustom/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClientCustom'))
                ->middleware(Authentication::class)
                ->action([ClientCustomController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/clientcustom/view/{id}')
                ->name('clientcustom/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClientCustom'))
                ->middleware(Authentication::class)
                ->action([ClientCustomController::class, 'view']),
                
            // ClientNote    
            Route::get('/clientnote')
                ->middleware(Authentication::class)
                ->action([ClientNoteController::class, 'index'])
                ->name('clientnote/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/clientnote/add')
                ->middleware(Authentication::class)
                ->action([ClientNoteController::class, 'add'])
                ->name('clientnote/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/clientnote/edit/{id}')
                ->name('clientnote/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClientNote'))
                ->middleware(Authentication::class)
                ->action([ClientNoteController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/clientnote/delete/{id}')
                ->name('clientnote/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClientNote'))
                ->middleware(Authentication::class)
                ->action([ClientNoteController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/clientnote/view/{id}')
                ->name('clientnote/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClientNote'))
                ->middleware(Authentication::class)
                ->action([ClientNoteController::class, 'view']),
            
            // Recurring    
            Route::get('/recurring')
                ->middleware(Authentication::class)
                ->action([RecurringController::class, 'index'])
                ->name('recurring/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/recurring/add')
                ->middleware(Authentication::class)
                ->action([RecurringController::class, 'add'])
                ->name('recurring/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/recurring/edit/{id}')
                ->name('recurring/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editRecurring'))
                ->middleware(Authentication::class)
                ->action([RecurringController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/recurring/delete/{id}')
                ->name('recurring/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editRecurring'))
                ->middleware(Authentication::class)
                ->action([RecurringController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/recurring/view/{id}')
                ->name('recurring/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editRecurring'))
                ->middleware(Authentication::class)
                ->action([RecurringController::class, 'view']),
            
            // Import    
            Route::get('/import')
                ->middleware(Authentication::class)
                ->action([ImportController::class, 'index'])
                ->name('import/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/import/add')
                ->middleware(Authentication::class)
                ->action([ImportController::class, 'add'])
                ->name('import/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/import/edit/{id}')
                ->name('import/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editImport'))
                ->middleware(Authentication::class)
                ->action([ImportController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/import/delete/{id}')
                ->name('import/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editImport'))
                ->middleware(Authentication::class)
                ->action([ImportController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/import/view/{id}')
                ->name('import/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editImport'))
                ->middleware(Authentication::class)
                ->action([ImportController::class, 'view']),
    
            Route::get('/quote/[page{page:\d+}]')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'index'])
                ->name('quote/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/quote/add')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'add'])
                ->name('quote/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/quote/edit/{id}')
                ->name('quote/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuote'))
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/quote/delete/{id}')
                ->name('quote/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuote'))
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/quote/view/{id}')
                ->name('quote/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuote'))
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'view']),    
            // QuoteItem
            Route::get('/quoteitem')
                ->middleware(Authentication::class)
                ->action([QuoteItemController::class, 'index'])
                ->name('quoteitem/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/quoteitem/add')
                ->middleware(Authentication::class)
                ->action([QuoteItemController::class, 'add'])
                ->name('quoteitem/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/quoteitem/edit/{id}')
                ->name('quoteitem/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteItem'))
                ->middleware(Authentication::class)
                ->action([QuoteItemController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/quoteitem/delete/{id}')
                ->name('quoteitem/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteItem'))
                ->middleware(Authentication::class)
                ->action([QuoteItemController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/quoteitem/view/{id}')
                ->name('quoteitem/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteItem'))
                ->middleware(Authentication::class)
                ->action([QuoteItemController::class, 'view']),                
            // QuoteItemAmount
            Route::get('/quoteitemamount')
                ->middleware(Authentication::class)
                ->action([QuoteItemAmountController::class, 'index'])
                ->name('quoteitemamount/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/quoteitemamount/add')
                ->middleware(Authentication::class)
                ->action([QuoteItemAmountController::class, 'add'])
                ->name('quoteitemamount/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/quoteitemamount/edit/{id}')
                ->name('quoteitemamount/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteItemAmount'))
                ->middleware(Authentication::class)
                ->action([QuoteItemAmountController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/quoteitemamount/delete/{id}')
                ->name('quoteitemamount/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteItemAmount'))
                ->middleware(Authentication::class)
                ->action([QuoteItemAmountController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/quoteitemamount/view/{id}')
                ->name('quoteitemamount/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteItemAmount'))
                ->middleware(Authentication::class)
                ->action([QuoteItemAmountController::class, 'view']),
                
            // QuoteAmount    
            Route::get('/quoteamount')
                ->middleware(Authentication::class)
                ->action([QuoteAmountController::class, 'index'])
                ->name('quoteamount/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/quoteamount/add')
                ->middleware(Authentication::class)
                ->action([QuoteAmountController::class, 'add'])
                ->name('quoteamount/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/quoteamount/edit/{id}')
                ->name('quoteamount/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteAmount'))
                ->middleware(Authentication::class)
                ->action([QuoteAmountController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/quoteamount/delete/{id}')
                ->name('quoteamount/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteAmount'))
                ->middleware(Authentication::class)
                ->action([QuoteAmountController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/quoteamount/view/{id}')
                ->name('quoteamount/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteAmount'))
                ->middleware(Authentication::class)
                ->action([QuoteAmountController::class, 'view']),
                
            // QuoteTaxRate    
            Route::get('/quotetaxrate')
                ->middleware(Authentication::class)
                ->action([QuoteTaxRateController::class, 'index'])
                ->name('quotetaxrate/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/quotetaxrate/add')
                ->middleware(Authentication::class)
                ->action([QuoteTaxRateController::class, 'add'])
                ->name('quotetaxrate/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/quotetaxrate/edit/{id}')
                ->name('quotetaxrate/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteTaxRate'))
                ->middleware(Authentication::class)
                ->action([QuoteTaxRateController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/quotetaxrate/delete/{id}')
                ->name('quotetaxrate/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteTaxRate'))
                ->middleware(Authentication::class)
                ->action([QuoteTaxRateController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/quotetaxrate/view/{id}')
                ->name('quotetaxrate/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteTaxRate'))
                ->middleware(Authentication::class)
                ->action([QuoteTaxRateController::class, 'view']),
                
            // InvTaxRate    
            Route::get('/invtaxrate')
                ->middleware(Authentication::class)
                ->action([InvTaxRateController::class, 'index'])
                ->name('invtaxrate/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/invtaxrate/add')
                ->middleware(Authentication::class)
                ->action([InvTaxRateController::class, 'add'])
                ->name('invtaxrate/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/invtaxrate/edit/{id}')
                ->name('invtaxrate/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvTaxRate'))
                ->middleware(Authentication::class)
                ->action([InvTaxRateController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/invtaxrate/delete/{id}')
                ->name('invtaxrate/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvTaxRate'))
                ->middleware(Authentication::class)
                ->action([InvTaxRateController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/invtaxrate/view/{id}')
                ->name('invtaxrate/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvTaxRate'))
                ->middleware(Authentication::class)
                ->action([InvTaxRateController::class, 'view']),
            
            
            // Payment
            Route::get('/payment')
                ->middleware(Authentication::class)
                ->action([PaymentController::class, 'index'])
                ->name('payment/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/payment/add')
                ->middleware(Authentication::class)
                ->action([PaymentController::class, 'add'])
                ->name('payment/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/payment/edit/{id}')
                ->name('payment/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPayment'))
                ->middleware(Authentication::class)
                ->action([PaymentController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/payment/delete/{id}')
                ->name('payment/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPayment'))
                ->middleware(Authentication::class)
                ->action([PaymentController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/payment/view/{id}')
                ->name('payment/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPayment'))
                ->middleware(Authentication::class)
                ->action([PaymentController::class, 'view']),
                         
            // PaymentMethod    
            Route::get('/paymentmethod')
                ->middleware(Authentication::class)
                ->action([PaymentMethodController::class, 'index'])
                ->name('paymentmethod/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/paymentmethod/add')
                ->middleware(Authentication::class)
                ->action([PaymentMethodController::class, 'add'])
                ->name('paymentmethod/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/paymentmethod/edit/{id}')
                ->name('paymentmethod/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPaymentMethod'))
                ->middleware(Authentication::class)
                ->action([PaymentMethodController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/paymentmethod/delete/{id}')
                ->name('paymentmethod/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPaymentMethod'))
                ->middleware(Authentication::class)
                ->action([PaymentMethodController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/paymentmethod/view/{id}')
                ->name('paymentmethod/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPaymentMethod'))
                ->middleware(Authentication::class)
                ->action([PaymentMethodController::class, 'view']),
           
            // PaymentCustom    
            Route::get('/paymentcustom')
                ->middleware(Authentication::class)
                ->action([PaymentCustomController::class, 'index'])
                ->name('paymentcustom/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/paymentcustom/add')
                ->middleware(Authentication::class)
                ->action([PaymentCustomController::class, 'add'])
                ->name('paymentcustom/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/paymentcustom/edit/{id}')
                ->name('paymentcustom/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPaymentCustom'))
                ->middleware(Authentication::class)
                ->action([PaymentCustomController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/paymentcustom/delete/{id}')
                ->name('paymentcustom/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPaymentCustom'))
                ->middleware(Authentication::class)
                ->action([PaymentCustomController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/paymentcustom/view/{id}')
                ->name('paymentcustom/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPaymentCustom'))
                ->middleware(Authentication::class)
                ->action([PaymentCustomController::class, 'view']),
            
        ),//invoice   
];