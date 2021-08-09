<?php

use App\Invoice\ClientNote\ClientNoteController
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

?>        