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
<form id="AmountForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
    
<input type="hidden" name="_csrf" value="<?= $csrf ?>">

<div id="headerbar">    
<h1 class="headerbar-title"><?= $s->trans('amounts_form'); ?></h1>

<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>

<?php echo (string)$response->getBody(); ?><div id="content">
    
<div class="row">
 <div class="mb3 form-group">
    <label for="inv_id">Inv</label>
    <select name="inv_id" id="inv_id" class="form-control simple-select">
       <option value="0">Inv</option>
         <?php foreach ($invs as $inv) { ?>
          <option value="<?= $inv->getId(); ?>"
           <?php $s->check_select(Html::encode($body['inv_id'] ?? ''), $inv->getId()) ?>
           ><?= $inv->getId(); ?></option>
         <?php } ?>
    </select>
</div>
 <div class="mb3 form-group">
   <label for="sign"><?= 'Sign Value'; ?></label>
   <input type="text" name="sign" id="sign" class="form-control"
 value="<?= Html::encode($body['sign'] ??  ''); ?>">
 </div>
<div class="form-group">
  <label for="item_sub_total"><?= $s->trans('subtotal'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="item_sub_total" id="item_sub_total" class="form-control"
              value="<?= $s->format_amount($body['item_sub_total'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="item_tax_total"><?= $s->trans('tax'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="item_tax_total" id="item_tax_total" class="form-control"
              value="<?= $s->format_amount($body['item_tax_total'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="tax_total"><?= 'Total Tax'; ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="tax_total" id="tax_total" class="form-control"
              value="<?= $s->format_amount($body['tax_total'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="invoice_total"><?= $s->trans('total'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="invoice_total" id="invoice_total" class="form-control"
              value="<?= $s->format_amount($body['invoice_total'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="invoice_paid"><?= $s->trans('total_paid'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="invoice_paid" id="invoice_paid" class="form-control"
              value="<?= $s->format_amount($body['invoice_paid'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="invoice_balance"><?= $s->trans('total_balance'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="invoice_balance" id="invoice_balance" class="form-control"
              value="<?= $s->format_amount($body['invoice_balance'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>

</div>
</div>
</div>
</form>
