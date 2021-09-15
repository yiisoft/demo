<?php

use App\Invoice\ItemLookup\ItemLookupController
    // ItemLookup    Route::get('/itemlookup')
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

?>        