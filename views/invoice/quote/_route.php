<?php

use App\Invoice\Quote\QuoteController
    // Quote    Route::get('/quote')
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

?>        