<?php

declare(strict_types=1);

use App\Blog\Archive\ArchiveController;
use App\Blog\BlogController;
use App\Blog\CommentController;
use App\Blog\Post\PostController;
use App\Blog\Post\PostRepository;
use App\Blog\Tag\TagController;
use App\Contact\ContactController;
use App\Controller\Actions\ApiInfo;
use App\Auth\Controller\AuthController;
use App\Auth\Controller\SignupController;
use App\Controller\SiteController;
use App\Middleware\AccessChecker;
use App\Middleware\ApiDataWrapper;
use App\User\Controller\ApiUserController;
use App\User\Controller\UserController;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsHtml;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsJson;
use Yiisoft\DataResponse\Middleware\FormatDataResponseAsXml;
use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Swagger\Middleware\SwaggerJson;
use Yiisoft\Swagger\Middleware\SwaggerUi;
use Yiisoft\Yii\Middleware\HttpCache;

use App\Invoice\Client\ClientController;
use App\Invoice\ClientCustom\ClientCustomController;
use App\Invoice\ClientNote\ClientNoteController;
use App\Invoice\Company\CompanyController;
use App\Invoice\CompanyPrivate\CompanyPrivateController;
use App\Invoice\CustomField\CustomFieldController;
use App\Invoice\CustomValue\CustomValueController;
use App\Invoice\EmailTemplate\EmailTemplateController;
use App\Invoice\Family\FamilyController;
use App\Invoice\Generator\GeneratorController;
use App\Invoice\GeneratorRelation\GeneratorRelationController;
use App\Invoice\Group\GroupController;
use App\Invoice\Import\ImportController;
use App\Invoice\UserClient\UserClientController;

// Invoice - Overall App Controller
use App\Invoice\InvoiceController;

// Inv - Invoice Controller
use App\Invoice\Inv\InvController;
use App\Invoice\InvAmount\InvAmountController;
use App\Invoice\InvCustom\InvCustomController;
use App\Invoice\InvItem\InvItemController;
use App\Invoice\InvItemAmount\InvItemAmountController;
use App\Invoice\InvRecurring\InvRecurringController;
use App\Invoice\InvTaxRate\InvTaxRateController;
use App\Invoice\ItemLookup\ItemLookupController;
use App\Invoice\Merchant\MerchantController;
use App\Invoice\Payment\PaymentController;
use App\Invoice\PaymentCustom\PaymentCustomController;
use App\Invoice\PaymentMethod\PaymentMethodController;
use App\Invoice\Product\ProductController;
use App\Invoice\Project\ProjectController;
use App\Invoice\Profile\ProfileController;

// Quote
use App\Invoice\Quote\QuoteController;
use App\Invoice\QuoteAmount\QuoteAmountController;
use App\Invoice\QuoteCustom\QuoteCustomController;
use App\Invoice\QuoteItem\QuoteItemController;
use App\Invoice\QuoteItemAmount\QuoteItemAmountController;
use App\Invoice\QuoteTaxRate\QuoteTaxRateController;
use App\Invoice\Setting\SettingController;
use App\Invoice\Sumex\SumexController;
use App\Invoice\Task\TaskController;
use App\Invoice\TaxRate\TaxRateController;
use App\Invoice\Unit\UnitController;
use App\Invoice\UserInv\UserInvController;
use App\Invoice\Upload\UploadController;

return [
    // Lonely pages of site
    Route::get('/')
        ->action([SiteController::class, 'index'])
        ->name('site/index'),
    Route::methods([Method::GET, Method::POST], '/contact')
        ->action([ContactController::class, 'contact'])
        ->name('site/contact'),

    // Auth
    Route::methods([Method::GET, Method::POST], '/login')
        ->action([AuthController::class, 'login'])
        ->name('auth/login'),
    Route::post('/logout')
        ->action([AuthController::class, 'logout'])
        ->name('auth/logout'),
    Route::methods([Method::GET, Method::POST], '/signup')
        ->action([SignupController::class, 'signup'])
        ->name('auth/signup'),

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
                ->middleware(
                    fn (HttpCache $httpCache, PostRepository $postRepository) =>
                    $httpCache->withLastModified(function (ServerRequestInterface $request, $params) use ($postRepository) {
                        return $postRepository
                            ->getMaxUpdatedAt()
                            ->getTimestamp();
                    })
                )
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
                ->middleware(Authentication::class)
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editPost'))
                ->action([PostController::class, 'edit']),

            // Post page
            Route::get('/page/{slug}')
                ->middleware(
                    fn (HttpCache $httpCache, PostRepository $postRepository, CurrentRoute $currentRoute) =>
                    $httpCache->withEtagSeed(function (ServerRequestInterface $request, $params) use ($postRepository, $currentRoute) {
                        $post = $postRepository->findBySlug($currentRoute->getArgument('slug'));
                        return $post->getSlug() . '-' . $post
                                ->getUpdatedAt()
                                ->getTimestamp();
                    })
                )
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
                ->action(SwaggerJson::class),
        ),
                            
    Group::create('/invoice')
        ->routes(
            Route::get('')
                ->middleware(Authentication::class)
                ->action([InvoiceController::class, 'index'])
                ->name('invoice/index'),            
            Route::get('/client[/page/{page:\d+}[/active/{active}]]')
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'index'])
                ->name('client/index'),
            Route::methods([Method::GET, Method::POST], '/add-a-client')
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'add'])
                ->name('client/add'),
            Route::methods([Method::GET, Method::POST], '/client/create_confirm')
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'create_confirm'])
                ->name('client/create_confirm'),
            Route::methods([Method::GET, Method::POST], '/edit-a-client/{id}')
                ->name('client/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'edit']),
            Route::methods([Method::GET, Method::POST], '/client/edit_submit')
                ->name('client/edit_submit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'edit_submit']),     
            Route::methods([Method::GET, Method::POST], '/client/delete/{id}')
                ->name('client/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/client/save_custom_fields')
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'save_custom_fields'])
                ->name('client/save_custom_fields'),    
            Route::methods([Method::GET, Method::POST], '/client/view/{id}')
                ->name('client/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'view']),
             Route::methods([Method::GET, Method::POST], '/client/view_client_custom_fields/{id}')
                ->name('client/view_client_custom_fields')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editClient'))
                ->middleware(Authentication::class)
                ->action([ClientController::class, 'view_client_custom_fields']),
            Route::get('/company')
                ->middleware(Authentication::class)
                ->action([CompanyController::class, 'index'])
                ->name('company/index'),    
            Route::methods([Method::GET, Method::POST], '/company/add')
                ->middleware(Authentication::class)
                ->action([CompanyController::class, 'add'])
                ->name('company/add'),
            Route::methods([Method::GET, Method::POST], '/company/edit/{id}')
                ->name('company/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editCompany'))
                ->middleware(Authentication::class)
                ->action([CompanyController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/company/delete/{id}')
                ->name('company/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editCompany'))
                ->middleware(Authentication::class)
                ->action([CompanyController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/company/view/{id}')
                ->name('company/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editCompany'))
                ->middleware(Authentication::class)
                ->action([CompanyController::class, 'view']),       
            Route::get('/companyprivate')
                ->middleware(Authentication::class)
                ->action([CompanyPrivateController::class, 'index'])
                ->name('companyprivate/index'), 
            Route::methods([Method::GET, Method::POST], '/companyprivate/add')
                ->middleware(Authentication::class)
                ->action([CompanyPrivateController::class, 'add'])
                ->name('companyprivate/add'),
            Route::methods([Method::GET, Method::POST], '/companyprivate/edit/{id}')
                ->name('companyprivate/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editCompanyPrivate'))
                ->middleware(Authentication::class)
                ->action([CompanyPrivateController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/companyprivate/delete/{id}')
                ->name('companyprivate/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editCompanyPrivate'))
                ->middleware(Authentication::class)
                ->action([CompanyPrivateController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/companyprivate/view/{id}')
                ->name('companyprivate/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editCompanyPrivate'))
                ->middleware(Authentication::class)
                ->action([CompanyPrivateController::class, 'view']), 
            Route::get('/customfield')
                ->middleware(Authentication::class)
                ->action([CustomFieldController::class, 'index'])
                ->name('customfield/index'),   
            Route::methods([Method::GET, Method::POST], '/customfield/add')
                ->middleware(Authentication::class)
                ->action([CustomFieldController::class, 'add'])
                ->name('customfield/add'),
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
            Route::get('/customvalue')
                ->middleware(Authentication::class)
                ->action([CustomValueController::class, 'index'])
                ->name('customvalue/index'),            
            Route::methods([Method::GET, Method::POST], '/customvalue/field/{id}')
                ->middleware(Authentication::class)
                ->action([CustomValueController::class, 'field'])
                ->name('customvalue/field'),     
            Route::methods([Method::GET, Method::POST], '/customvalue/new/{id}')
                ->middleware(Authentication::class)
                ->action([CustomValueController::class, 'new'])
                ->name('customvalue/new'),
            Route::methods([Method::GET, Method::POST], '/customvalue/add')
                ->middleware(Authentication::class)
                ->action([CustomValueController::class, 'add'])
                ->name('customvalue/add'),
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
            Route::get('/clientcustom')
                ->middleware(Authentication::class)
                ->action([ClientCustomController::class, 'index'])
                ->name('clientcustom/index'),    
            Route::methods([Method::GET, Method::POST], '/clientcustom/add')
                ->middleware(Authentication::class)
                ->action([ClientCustomController::class, 'add'])
                ->name('clientcustom/add'),
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
            Route::get('/clientnote')
                ->middleware(Authentication::class)
                ->action([ClientNoteController::class, 'index'])
                ->name('clientnote/index'),    
            Route::methods([Method::GET, Method::POST], '/clientnote/add')
                ->middleware(Authentication::class)
                ->action([ClientNoteController::class, 'add'])
                ->name('clientnote/add'),
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
            Route::get('/emailtemplate')
                ->middleware(Authentication::class)
                ->action([EmailTemplateController::class, 'index'])
                ->name('emailtemplate/index'),
            Route::methods([Method::GET, Method::POST], '/emailtemplate/add')
                ->middleware(Authentication::class)
                ->action([EmailTemplateController::class, 'add'])
                ->name('emailtemplate/add'),
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
            Route::methods([Method::GET, Method::POST], '/family/test')
                ->middleware(FormatDataResponseAsJson::class)
                ->action([FamilyController::class])
                ->name('family/test'),
            Route::methods([Method::GET, Method::POST], '/family/add')
                ->middleware(Authentication::class)
                ->action([FamilyController::class, 'add'])
                ->name('family/add'),
            Route::methods([Method::GET, Method::POST], '/family/edit/{id}')
                ->name('family/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editFamily'))
                ->middleware(Authentication::class)
                ->action([FamilyController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/family/delete/{id}')
                ->name('family/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editFamily'))
                ->middleware(Authentication::class)
                ->action([FamilyController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/family/view/{id}')
                ->name('family/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editFamily'))
                ->middleware(Authentication::class)
                ->action([FamilyController::class, 'view']),  
            Route::get('/generator')
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'index'])
                ->name('generator/index'),    
            Route::methods([Method::GET, Method::POST], '/generator/add')
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, 'add'])
                ->name('generator/add'),
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
            Route::methods([Method::GET, Method::POST], '/generator/_index_adv_paginator_with_filter/{id}')
                ->name('generator/_index_adv_paginator_with_filter')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editGenerator'))
                ->middleware(Authentication::class)
                ->action([GeneratorController::class, '_index_adv_paginator_with_filter']),    
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
             Route::get('/generatorrelation')
                ->middleware(Authentication::class)
                ->action([GeneratorRelationController::class, 'index'])
                ->name('generatorrelation/index'), 
            Route::methods([Method::GET, Method::POST], '/generatorrelation/add')
                ->middleware(Authentication::class)
                ->action([GeneratorRelationController::class, 'add'])
                ->name('generatorrelation/add'),
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
            Route::get('/group')
                ->middleware(Authentication::class)
                ->action([GroupController::class, 'index'])
                ->name('group/index'),
            Route::methods([Method::GET, Method::POST], '/group/add')
                ->middleware(Authentication::class)
                ->action([GroupController::class, 'add'])
                ->name('group/add'),
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
            Route::get('/inv[/page/{page:\d+}[/status/{status:\d+}]]')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'index'])
                ->name('inv/index'),
            Route::methods([Method::GET, Method::POST], '/add-a-inv')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'add'])
                ->name('inv/add'),
            Route::methods([Method::GET, Method::POST], '/archive')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'archive'])
                ->name('inv/archive'),
            Route::methods([Method::GET, Method::POST], '/inv/save_custom')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'save_custom'])
                ->name('inv/save_custom'),    
            Route::methods([Method::GET, Method::POST], '/inv/save_inv_tax_rate')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'save_inv_tax_rate'])
                ->name('inv/save_inv_tax_rate'),
            Route::methods([Method::GET, Method::POST], '/inv/delete_inv_tax_rate/{id}')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'delete_inv_tax_rate'])
                ->name('inv/delete_inv_tax_rate'),
            Route::methods([Method::GET, Method::POST], '/inv/delete_inv_item/{id}')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'delete_inv_item'])
                ->name('inv/delete_inv_item'),
            Route::methods([Method::GET, Method::POST], '/inv/pdf/{include}')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'pdf'])
                ->name('inv/pdf'),
            Route::methods([Method::GET, Method::POST], '/inv/save_inv_item')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'save_inv_item'])
                ->name('inv/save_inv_item'),        
            Route::methods([Method::GET, Method::POST], '/inv/modalcreate')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'modalcreate'])
                ->name('inv/modalcreate'),
            Route::methods([Method::GET, Method::POST], '/inv/confirm')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'confirm'])
                ->name('inv/confirm'),
            Route::methods([Method::GET, Method::POST], '/inv/create_confirm')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'create_confirm'])
                ->name('inv/create_confirm'),
            Route::methods([Method::GET, Method::POST], '/inv/create_credit_confirm')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'create_credit_confirm'])
                ->name('inv/create_credit_confirm'),
            Route::methods([Method::GET, Method::POST], '/inv/download/{invoice}')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'download'])
                ->name('inv/download'),
            Route::methods([Method::GET, Method::POST], '/inv/inv_to_inv_confirm')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'inv_to_inv_confirm'])
                ->name('inv/inv_to_inv_confirm'),
            // InvRecurring    
            Route::get('/invrecurring')
                ->middleware(Authentication::class)
                ->action([InvRecurringController::class, 'index'])
                ->name('invrecurring/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/invrecurring/add')
                ->middleware(Authentication::class)
                ->action([InvRecurringController::class, 'add'])
                ->name('invrecurring/add'),
            // Create via inv.js create_recurring_confirm
            Route::methods([Method::GET, Method::POST], '/invrecurring/create_recurring_confirm')
                ->middleware(Authentication::class)
                ->action([InvRecurringController::class, 'create_recurring_confirm'])
                ->name('invrecurring/create_recurring_confirm'),
            Route::methods([Method::GET, Method::POST], '/invrecurring/get_recur_start_date')
                ->middleware(Authentication::class)
                ->action([InvRecurringController::class, 'get_recur_start_date'])
                ->name('invrecurring/get_recur_start_date'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/invrecurring/edit/{id}')
                ->name('invrecurring/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvRecurring'))
                ->middleware(Authentication::class)
                ->action([InvRecurringController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/invrecurring/delete/{id}')
                ->name('invrecurring/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvRecurring'))
                ->middleware(Authentication::class)
                ->action([InvRecurringController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/invrecurring/stop/{id}')
                ->name('invrecurring/stop')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvRecurring'))
                ->middleware(Authentication::class)
                ->action([InvRecurringController::class, 'stop']),
            Route::methods([Method::GET, Method::POST], '/invrecurring/view/{id}')
                ->name('invrecurring/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvRecurring'))
                ->middleware(Authentication::class)
                ->action([InvRecurringController::class, 'view']),
            Route::methods([Method::GET, Method::POST], '/inv/modal_change_client')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'modal_change_client'])
                ->name('inv/modal_change_client'),
            Route::methods([Method::GET, Method::POST], '/inv/edit/{id}')
                ->name('inv/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInv'))
                ->middleware(Authentication::class)
                ->action([InvController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/inv/test')
                ->action([InvController::class, 'test'])
                ->name('inv/test'),
            Route::methods([Method::GET, Method::POST], '/inv/save')
                ->middleware(Authentication::class)
                ->action([InvController::class, 'save'])
                ->name('inv/save'),
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
            Route::get('/invamount')
                ->middleware(Authentication::class)
                ->action([InvAmountController::class, 'index'])
                ->name('invamount/index'),    
            Route::methods([Method::GET, Method::POST], '/invamount/add')
                ->middleware(Authentication::class)
                ->action([InvAmountController::class, 'add'])
                ->name('invamount/add'),
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
            Route::get('/invcustom')
                ->middleware(Authentication::class)
                ->action([InvCustomController::class, 'index'])
                ->name('invcustom/index'),    
            Route::methods([Method::GET, Method::POST], '/invcustom/add')
                ->middleware(Authentication::class)
                ->action([InvCustomController::class, 'add'])
                ->name('invcustom/add'),
            Route::methods([Method::GET, Method::POST], '/invcustom/edit/{id}')
                ->name('invcustom/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvCustom'))
                ->middleware(Authentication::class)
                ->action([InvCustomController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/invcustom/delete/{id}')
                ->name('invcustom/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvCustom'))
                ->middleware(Authentication::class)
                ->action([InvCustomController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/invcustom/view/{id}')
                ->name('invcustom/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvCustom'))
                ->middleware(Authentication::class)
                ->action([InvCustomController::class, 'view']),    
            Route::get('/invitem')
                ->middleware(Authentication::class)
                ->action([InvItemController::class, 'index'])
                ->name('invitem/index'),    
            Route::methods([Method::POST], '/invitem/add')
                ->middleware(Authentication::class)
                ->action([InvItemController::class, 'add'])
                ->name('invitem/add'),
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
            Route::methods([Method::GET, Method::POST], '/invitem/multiple')
                ->name('invitem/multiple')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvItem'))
                ->middleware(Authentication::class)
                ->action([InvItemController::class, 'multiple']),
            Route::methods([Method::GET, Method::POST], '/invitem/view/{id}')
                ->name('invitem/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvItem'))
                ->middleware(Authentication::class)
                ->action([InvItemController::class, 'view']),  
            Route::get('/invitemamount')
                ->middleware(Authentication::class)
                ->action([InvItemAmountController::class, 'index'])
                ->name('invitemamount/index'),    
            Route::methods([Method::GET, Method::POST], '/invitemamount/add')
                ->middleware(Authentication::class)
                ->action([InvItemAmountController::class, 'add'])
                ->name('invitemamount/add'),
            Route::methods([Method::GET, Method::POST], '/invitemamount/edit/{id}')
                ->name('invitemamount/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvItemAmount'))
                ->middleware(Authentication::class)
                ->action([InvItemAmountController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/invitemamount/delete/{id}')
                ->name('invitemamount/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvItemAmount'))
                ->middleware(Authentication::class)
                ->action([InvItemAmountController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/invitemamount/view/{id}')
                ->name('invitemamount/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editInvItemAmount'))
                ->middleware(Authentication::class)
                ->action([InvItemAmountController::class, 'view']), 
            Route::get('/invtaxrate')
                ->middleware(Authentication::class)
                ->action([InvTaxRateController::class, 'index'])
                ->name('invtaxrate/index'),    
            Route::methods([Method::GET, Method::POST], '/invtaxrate/add')
                ->middleware(Authentication::class)
                ->action([InvTaxRateController::class, 'add'])
                ->name('invtaxrate/add'),
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
            Route::get('/itemlookup')
                ->middleware(Authentication::class)
                ->action([ItemLookupController::class, 'index'])
                ->name('itemlookup/index'),    
            Route::methods([Method::GET, Method::POST], '/itemlookup/add')
                ->middleware(Authentication::class)
                ->action([ItemLookupController::class, 'add'])
                ->name('itemlookup/add'),
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
            
            Route::get('/product[/page/{page:\d+}]')
                ->middleware(Authentication::class)
                ->action([ProductController::class, 'index'])
                ->name('product/index'),
            Route::methods([Method::GET, Method::POST],'/product/test')
                ->middleware(Authentication::class)
                ->middleware(FormatDataResponseAsJson::class)
                ->action([ProductController::class, 'test'])
                ->name('product/test'),  
            Route::methods([Method::GET, Method::POST],'/product/add')
                ->middleware(Authentication::class)
                ->action([ProductController::class, 'add'])
                ->name('product/add'),  
            Route::methods([Method::GET, Method::POST],'/product/lookup')
                ->middleware(Authentication::class)
                ->action([ProductController::class, 'lookup'])
                ->name('product/lookup'),
            Route::get('/product/selection_quote') 
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProduct'))
                ->action([ProductController::class, 'selection_quote'])
                ->name('product/selection_quote'),
            Route::get('/product/selection_inv') 
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProduct'))
                ->action([ProductController::class, 'selection_inv'])
                ->name('product/selection_inv'),    
            Route::methods([Method::GET, Method::POST], '/product/edit/{id}')
                ->name('product/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProduct'))
                ->middleware(Authentication::class)
                ->action([ProductController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/product/delete/{id}')
                ->name('product/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProduct'))
                ->middleware(Authentication::class)
                ->action([ProductController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/product/view/{id}')
                ->name('product/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProduct'))
                ->middleware(Authentication::class)
                ->action([ProductController::class, 'view']), 
            Route::get('/profile')
                ->middleware(Authentication::class)
                ->action([ProfileController::class, 'index'])
                ->name('profile/index'),    
            Route::methods([Method::GET, Method::POST], '/profile/add')
                ->middleware(Authentication::class)
                ->action([ProfileController::class, 'add'])
                ->name('profile/add'),
            Route::methods([Method::GET, Method::POST], '/profile/edit/{id}')
                ->name('profile/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProfile'))
                ->middleware(Authentication::class)
                ->action([ProfileController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/profile/delete/{id}')
                ->name('profile/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProfile'))
                ->middleware(Authentication::class)
                ->action([ProfileController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/profile/view/{id}')
                ->name('profile/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editProfile'))
                ->middleware(Authentication::class)
                ->action([ProfileController::class, 'view']),    
            Route::get('/project[/page/{page:\d+}]')
                ->middleware(Authentication::class)
                ->action([ProjectController::class, 'index'])
                ->name('project/index'),   
            Route::methods([Method::GET, Method::POST], '/project/add')
                ->middleware(Authentication::class)
                ->action([ProjectController::class, 'add'])
                ->name('project/add'),
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
            Route::get('/setting[page{page:\d+}]')
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'debug_index'])
                ->name('setting/debug_index'),
             Route::methods([Method::GET, Method::POST], '/setting/save')
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'save'])
                ->name('setting/save'),    
            Route::methods([Method::GET, Method::POST], '/setting/tab_index')
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'tab_index'])
                ->name('setting/tab_index'),        
            Route::methods([Method::GET, Method::POST], '/setting/add')
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'add'])
                ->name('setting/add'),
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
            Route::methods([Method::GET, Method::POST], '/setting/index')
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'index'])
                ->name('setting/index'),        
            Route::methods([Method::GET, Method::POST], '/setting/get_cron_key')
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'get_cron_key'])                
                ->name('setting/get_cron_key'),
            Route::methods([Method::GET, Method::POST], '/setting/view/{setting_id}')
                ->name('setting/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editSetting'))
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'view']),                
            Route::methods([Method::GET, Method::POST], '/setting/clear')
                ->middleware(Authentication::class)
                ->action([SettingController::class, 'clear'])                
                ->name('setting/clear'),
            Route::get('/task[/page/{page:\d+}]')
                ->middleware(Authentication::class)
                ->action([TaskController::class, 'index'])
                ->name('task/index'),
            Route::methods([Method::GET, Method::POST], '/task/add')
                ->middleware(Authentication::class)
                ->action([TaskController::class, 'add'])
                ->name('task/add'),
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
            Route::get('/taxrate')
                ->middleware(Authentication::class)
                ->action([TaxRateController::class, 'index'])
                ->name('taxrate/index'),
            Route::methods([Method::GET, Method::POST], '/taxrate/add')
                ->middleware(Authentication::class)
                ->action([TaxRateController::class, 'add'])
                ->name('taxrate/add'),
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
            Route::methods([Method::GET, Method::POST], '/unit/add')
                ->middleware(Authentication::class)
                ->action([UnitController::class, 'add'])
                ->name('unit/add'),
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
            Route::get('/import')
                ->middleware(Authentication::class)
                ->action([ImportController::class, 'index'])
                ->name('import/index'),
            Route::methods([Method::GET, Method::POST], '/import/add')
                ->middleware(Authentication::class)
                ->action([ImportController::class, 'add'])
                ->name('import/add'),
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
            Route::get('/merchant')
                ->middleware(Authentication::class)
                ->action([MerchantController::class, 'index'])
                ->name('merchant/index'),    
            Route::methods([Method::GET, Method::POST], '/merchant/add')
                ->middleware(Authentication::class)
                ->action([MerchantController::class, 'add'])
                ->name('merchant/add'),
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
            Route::get('/payment')
                ->middleware(Authentication::class)
                ->action([PaymentController::class, 'index'])
                ->name('payment/index'),    
            Route::methods([Method::GET, Method::POST], '/payment/add')
                ->middleware(Authentication::class)
                ->action([PaymentController::class, 'add'])
                ->name('payment/add'),
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
            Route::get('/paymentmethod')
                ->middleware(Authentication::class)
                ->action([PaymentMethodController::class, 'index'])
                ->name('paymentmethod/index'),    
            Route::methods([Method::GET, Method::POST], '/paymentmethod/add')
                ->middleware(Authentication::class)
                ->action([PaymentMethodController::class, 'add'])
                ->name('paymentmethod/add'),
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
            Route::get('/paymentcustom')
                ->middleware(Authentication::class)
                ->action([PaymentCustomController::class, 'index'])
                ->name('paymentcustom/index'),    
            Route::methods([Method::GET, Method::POST], '/paymentcustom/add')
                ->middleware(Authentication::class)
                ->action([PaymentCustomController::class, 'add'])
                ->name('paymentcustom/add'),
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
            Route::get('/quote[/page/{page:\d+}[/status/{status:\d+}]]')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'index'])
                ->name('quote/index'),
            Route::methods([Method::GET, Method::POST], '/add-a-quote')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'add'])
                ->name('quote/add'),
            Route::methods([Method::GET, Method::POST], '/quote/save_custom')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'save_custom'])
                ->name('quote/save_custom'),    
            Route::methods([Method::GET, Method::POST], '/quote/save_quote_tax_rate')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'save_quote_tax_rate'])
                ->name('quote/save_quote_tax_rate'),
            Route::methods([Method::GET, Method::POST], '/quote/delete_quote_tax_rate/{id}')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'delete_quote_tax_rate'])
                ->name('quote/delete_quote_tax_rate'),
            Route::methods([Method::GET, Method::POST], '/quote/delete_quote_item/{id}')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'delete_quote_item'])
                ->name('quote/delete_quote_item'),
            Route::methods([Method::GET, Method::POST], '/quote/pdf/{include}')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'pdf'])
                ->name('quote/pdf'),
            Route::methods([Method::GET, Method::POST], '/quote/save_quote_item')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'save_quote_item'])
                ->name('quote/save_quote_item'),        
            Route::methods([Method::GET, Method::POST], '/quote/modalcreate')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'modalcreate'])
                ->name('quote/modalcreate'),
            Route::methods([Method::GET, Method::POST], '/quote/confirm')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'confirm'])
                ->name('quote/confirm'),
            Route::methods([Method::GET, Method::POST], '/quote/create_confirm')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'create_confirm'])
                ->name('quote/create_confirm'),
            Route::methods([Method::GET, Method::POST], '/quote/quote_to_invoice_confirm')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'quote_to_invoice_confirm'])
                ->name('quote/quote_to_invoice_confirm'),
            Route::methods([Method::GET, Method::POST], '/quote/quote_to_quote_confirm')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'quote_to_quote_confirm'])
                ->name('quote/quote_to_quote_confirm'),
            Route::methods([Method::GET, Method::POST], '/quote/modal_change_client')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'modal_change_client'])
                ->name('quote/modal_change_client'),
            Route::methods([Method::GET, Method::POST], '/quote/edit/{id}')
                ->name('quote/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuote'))
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/quote/test')
                ->action([QuoteController::class, 'test'])
                ->name('quote/test'),
            Route::methods([Method::GET, Method::POST], '/quote/save')
                ->middleware(Authentication::class)
                ->action([QuoteController::class, 'save'])
                ->name('quote/save'),
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
            Route::get('/quoteamount')
                ->middleware(Authentication::class)
                ->action([QuoteAmountController::class, 'index'])
                ->name('quoteamount/index'),    
            Route::methods([Method::GET, Method::POST], '/quoteamount/add')
                ->middleware(Authentication::class)
                ->action([QuoteAmountController::class, 'add'])
                ->name('quoteamount/add'),
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
            Route::get('/quotecustom')
                ->middleware(Authentication::class)
                ->action([QuoteCustomController::class, 'index'])
                ->name('quotecustom/index'),    
            Route::methods([Method::GET, Method::POST], '/quotecustom/add')
                ->middleware(Authentication::class)
                ->action([QuoteCustomController::class, 'add'])
                ->name('quotecustom/add'),
            Route::methods([Method::GET, Method::POST], '/quotecustom/edit/{id}')
                ->name('quotecustom/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteCustom'))
                ->middleware(Authentication::class)
                ->action([QuoteCustomController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/quotecustom/delete/{id}')
                ->name('quotecustom/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteCustom'))
                ->middleware(Authentication::class)
                ->action([QuoteCustomController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/quotecustom/view/{id}')
                ->name('quotecustom/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteCustom'))
                ->middleware(Authentication::class)
                ->action([QuoteCustomController::class, 'view']),    
            Route::get('/quoteitem')
                ->middleware(Authentication::class)
                ->action([QuoteItemController::class, 'index'])
                ->name('quoteitem/index'),    
            Route::methods([Method::POST], '/quoteitem/add')
                ->middleware(Authentication::class)
                ->action([QuoteItemController::class, 'add'])
                ->name('quoteitem/add'),
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
            Route::methods([Method::GET, Method::POST], '/quoteitem/multiple')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteItem'))
                ->middleware(Authentication::class)
                ->action([QuoteItemController::class, 'multiple'])
                ->name('quoteitem/delete_multiple'),    
            Route::methods([Method::GET, Method::POST], '/quoteitem/view/{id}')
                ->name('quoteitem/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editQuoteItem'))
                ->middleware(Authentication::class)
                ->action([QuoteItemController::class, 'view']),  
            Route::get('/quoteitemamount')
                ->middleware(Authentication::class)
                ->action([QuoteItemAmountController::class, 'index'])
                ->name('quoteitemamount/index'),    
            Route::methods([Method::GET, Method::POST], '/quoteitemamount/add')
                ->middleware(Authentication::class)
                ->action([QuoteItemAmountController::class, 'add'])
                ->name('quoteitemamount/add'),
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
            Route::get('/quotetaxrate')
                ->middleware(Authentication::class)
                ->action([QuoteTaxRateController::class, 'index'])
                ->name('quotetaxrate/index'),    
            Route::methods([Method::GET, Method::POST], '/quotetaxrate/add')
                ->middleware(Authentication::class)
                ->action([QuoteTaxRateController::class, 'add'])
                ->name('quotetaxrate/add'),
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
            Route::get('/sumex')
                ->middleware(Authentication::class)
                ->action([SumexController::class, 'index'])
                ->name('sumex/index'),    
            Route::methods([Method::GET, Method::POST], '/sumex/add')
                ->middleware(Authentication::class)
                ->action([SumexController::class, 'add'])
                ->name('sumex/add'),
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
            // UserClient    
            Route::get('/userclient')
                ->middleware(Authentication::class)
                ->action([UserClientController::class, 'index'])
                ->name('userclient/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/userclient/add')
                ->middleware(Authentication::class)
                ->action([UserClientController::class, 'add'])
                ->name('userclient/add'),
             Route::methods([Method::GET, Method::POST], '/userclient/new/{user_id}')
                ->middleware(Authentication::class)
                ->action([UserClientController::class, 'new'])
                ->name('userclient/new'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/userclient/edit/{id}')
                ->name('userclient/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editUserClient'))
                ->middleware(Authentication::class)
                ->action([UserClientController::class, 'edit']), 
            Route::methods([Method::GET, Method::POST], '/userclient/delete/{id}')
                ->name('userclient/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editUserClient'))
                ->middleware(Authentication::class)
                ->action([UserClientController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/userclient/view/{id}')
                ->name('userclient/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editUserClient'))
                ->middleware(Authentication::class)
                ->action([UserClientController::class, 'view']),           
            // UserInv
            Route::get('/userinv[/page/{page:\d+}[/active/{active}]]')
                ->middleware(Authentication::class)
                ->action([UserInvController::class, 'index'])
                ->name('userinv/index'),    
            // Add
            Route::methods([Method::GET, Method::POST], '/userinv/add')
                ->middleware(Authentication::class)
                ->action([UserInvController::class, 'add'])
                ->name('userinv/add'),
            // Edit 
            Route::methods([Method::GET, Method::POST], '/userinv/edit/{id}')
                ->name('userinv/edit')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editUserInv'))
                ->middleware(Authentication::class)
                ->action([UserInvController::class, 'edit']),
             Route::methods([Method::GET, Method::POST], '/userinv/client/{id}')
                ->name('userinv/client')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editUserInv'))
                ->middleware(Authentication::class)
                ->action([UserInvController::class, 'client']),    
            Route::methods([Method::GET, Method::POST], '/userinv/delete/{id}')
                ->name('userinv/delete')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editUserInv'))
                ->middleware(Authentication::class)
                ->action([UserInvController::class, 'delete']),
            Route::methods([Method::GET, Method::POST], '/userinv/view/{id}')
                ->name('userinv/view')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editUserInv'))
                ->middleware(Authentication::class)
                ->action([UserInvController::class, 'view']),               
            Route::methods([Method::GET, Method::POST, Method::PUT, Method::PATCH], '/upload/upload_file')
                ->middleware(Authentication::class)
                ->action([UploadController::class, 'upload_file'])
                ->name('upload/upload_file'),    
            Route::methods([Method::GET, Method::POST], '/upload/add')
                ->middleware(Authentication::class)
                ->action([UploadController::class, 'add'])
                ->name('upload/add'),
            Route::methods([Method::GET, Method::POST], '/upload/view')
                ->middleware(Authentication::class)
                ->action([UploadController::class, 'view'])
                ->name('upload/view'),
            Route::methods([Method::GET, Method::POST], '/upload/get_file')
                ->name('upload/get_file')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editUpload'))
                ->middleware(Authentication::class)
                ->action([UploadController::class, 'get_file']), 
            Route::methods([Method::GET, Method::POST], '/upload/delete_file/{url_key}')
                ->name('upload/delete_file')
                ->middleware(fn (AccessChecker $checker) => $checker->withPermission('editUpload'))
                ->middleware(Authentication::class)
                ->action([UploadController::class, 'delete_file']),           
        ),//invoice  
];
