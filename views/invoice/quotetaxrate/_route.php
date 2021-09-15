<?php

use App\Invoice\QuoteTaxRate\QuoteTaxRateController
    // QuoteTaxRate    Route::get('/quotetaxrate')
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

?>        