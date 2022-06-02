<?php

declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var string $title
 */

if (!empty($errors)) {
    foreach ($errors as $field => $error) {
        echo Alert::widget()->options(['class' => 'alert-danger'])->body(Html::encode($field . ':' . $error));
    }
}

?>
<h1><?= Html::encode($title) ?></h1>
<div class="row">
 <div class="mb3 form-group">
   <label for="id" class="form-label" style="background:lightblue"><?= $s->trans('id'); ?></label>
   <?= Html::encode($body['id'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="sign" class="form-label" style="background:lightblue"><?= 'Sign'; ?></label>
   <?= Html::encode($body['sign'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="item_subtotal" class="form-label" style="background:lightblue"><?= 'Item Sub Total'; ?></label>
   <?= Html::encode($body['item_subtotal'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="item_tax_total" class="form-label" style="background:lightblue"><?= 'Item Tax Total'; ?></label>
   <?= Html::encode($body['item_tax_total'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="tax_total" class="form-label" style="background:lightblue"><?= 'Tax Total'; ?></label>
   <?= Html::encode($body['tax_total'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="invoice_total" class="form-label" style="background:lightblue"><?= 'Invoice Total'; ?></label>
   <?= Html::encode($body['invoice_total'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="invoice_paid" class="form-label" style="background:lightblue"><?= 'Invoice Paid'; ?></label>
   <?= Html::encode($body['invoice_paid'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="invoice_balance" class="form-label" style="background:lightblue"><?= 'Invoice Balance'; ?></label>
   <?= Html::encode($body['invoice_balance'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="inv_id" class="form-label" style="background:lightblue"><?= 'Invoice Id'; ?></label>
   <?= $amount->getInv()->getId();?>
 </div>
</div>
