<?php

use App\Invoice\QuoteAmount\QuoteAmountController
    // QuoteAmount    Route::get('/quoteamount')
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

?>        