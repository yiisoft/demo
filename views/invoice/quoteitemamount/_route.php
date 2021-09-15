<?php

use App\Invoice\QuoteItemAmount\QuoteItemAmountController
    // QuoteItemAmount    Route::get('/quoteitemamount')
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

?>        