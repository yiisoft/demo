<?php

use App\Invoice\PaymentCustom\PaymentCustomController
    // PaymentCustom    Route::get('/paymentcustom')
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

?>        