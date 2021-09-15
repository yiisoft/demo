<?php

use App\Invoice\QuoteItem\QuoteItemController
    // QuoteItem    Route::get('/quoteitem')
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

?>        