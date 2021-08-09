<?php  
     echo "<?php\n";             
?>

use App\Invoice\<?= $generator->getCamelcase_capital_name(); ?>\<?= $generator->getCamelcase_capital_name().'Controller'; ?>

    // <?= $generator->getCamelcase_capital_name(); ?>
    Route::get('/<?= $generator->getRoute_suffix(); ?>')
        ->middleware(Authentication::class)
        ->action([<?= $generator->getCamelcase_capital_name(); ?>Controller::class, 'index'])
        ->name('<?= $generator->getRoute_suffix(); ?>/index'),    
    // Add
    Route::methods([Method::GET, Method::POST], '/<?= $generator->getRoute_suffix(); ?>/add')
        ->middleware(Authentication::class)
        ->action([<?= $generator->getCamelcase_capital_name(); ?>Controller::class, 'add'])
        ->name('<?= $generator->getRoute_suffix(); ?>/add'),
    // Edit 
    Route::methods([Method::GET, Method::POST], '/<?= $generator->getRoute_suffix(); ?>/edit/{id}')
        ->name('<?= $generator->getRoute_suffix(); ?>/edit')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission('edit<?= $generator->getCamelcase_capital_name(); ?>'))
        ->middleware(Authentication::class)
        ->action([<?= $generator->getCamelcase_capital_name(); ?>Controller::class, 'edit']), 
    Route::methods([Method::GET, Method::POST], '/<?= $generator->getRoute_suffix(); ?>/delete/{id}')
        ->name('<?= $generator->getRoute_suffix(); ?>/delete')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission('edit<?= $generator->getCamelcase_capital_name(); ?>'))
        ->middleware(Authentication::class)
        ->action([<?= $generator->getCamelcase_capital_name(); ?>Controller::class, 'delete']),
    Route::methods([Method::GET, Method::POST], '/<?= $generator->getRoute_suffix(); ?>/view/{id}')
        ->name('<?= $generator->getRoute_suffix(); ?>/view')
        ->middleware(fn (AccessChecker $checker) => $checker->withPermission('edit<?= $generator->getCamelcase_capital_name(); ?>'))
        ->middleware(Authentication::class)
        ->action([<?= $generator->getCamelcase_capital_name(); ?>Controller::class, 'view']),

<?php  
     echo "?>";             
?>        