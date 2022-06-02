<?php

declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use App\Invoice\Helpers\DateHelper;

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
<form id="QuoteAmountForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('quoteamounts_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="quote_id">Quote</label>
    <select name="quote_id" id="quote_id" class="form-control simple-select">
       <option value="0">Quote</option>
         <?php foreach ($quotes as $quote) { ?>
          <option value="<?= $quote->getId(); ?>"
           <?php $s->check_select(Html::encode($body['quote_id'] ?? ''), $quote->getId()) ?>
           ><?= $quote->getId(); ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <input type="hidden" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
<div class="form-group">
  <label for="item_subtotal">Quote Item Subtotal</label>
      <div class="input-group has-feedback">
          <input type="text" name="item_subtotal" id="item_subtotal" class="form-control"
              value="<?= $s->format_amount($body['item_subtotal'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="item_tax_total">Item Tax Total</label>
      <div class="input-group has-feedback">
          <input type="text" name="item_tax_total" id="item_tax_total" class="form-control"
              value="<?= $s->format_amount($body['item_tax_total'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="tax_total">Tax Total</label>
      <div class="input-group has-feedback">
          <input type="text" name="tax_total" id="tax_total" class="form-control"
              value="<?= $s->format_amount($body['tax_total'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="total">Total</label>
      <div class="input-group has-feedback">
          <input type="text" name="total" id="total" class="form-control"
              value="<?= $s->format_amount($body['total'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
</div>
</div>
</div>
</form>
