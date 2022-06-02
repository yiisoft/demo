<?php
declare(strict_types=1);

use Yiisoft\Yii\Bootstrap5\Alert;

/**
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 */

?>
<?php
    $danger = $flash->get('danger');
    if ($danger != null) {
        $alert =  Alert::widget()
            ->body($danger)
            ->options([
                'class' => ['alert-danger shadow'],
            ])
        ->render();
        echo $alert;
    }
    $info = $flash->get('info');
    if ($info != null) {
        $alert =  Alert::widget()
            ->body($info)
            ->options([
                'class' => ['alert-info shadow'],
            ])
        ->render();
        echo $alert;
    }
    $warning = $flash->get('warning');
    if ($warning != null) {
        $alert =  Alert::widget()
            ->body($warning)
            ->options([
                'class' => ['alert-warning shadow'],
            ])
        ->render();
        echo $alert;
    }
?> 

<div id="headerbar">
    <h1 class="headerbar-title"><?= $s->trans('invoice_archive'); ?></h1>
    <div class="headerbar-item pull-right">
       <!-- No Url Generator here. Just post -->
       <form method="post">
            <input type="hidden" name="_csrf" value="<?= $csrf; ?>">
            <div class="input-group" hidden>
                <label for="invoice_number"><?= $s->trans('invoice_number'); ?></label>
                <input name="invoice_number" id="invoice_number" type="text" class="form-control input-sm" value="<?= $body['invoice_number'] ?? ''; ?>">
                <span class="input-group-btn">
                    <button class="btn btn-primary btn-sm" type="submit"><?= $s->trans('filter_invoices'); ?></button>
                </span>
            </div>
        </form>
    </div>
</div>
<div id="content" class="table-content">
    <div id="filter_results">
        <?= $partial_inv_archive; ?>
    </div>
</div>
