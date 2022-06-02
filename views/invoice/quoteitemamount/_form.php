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
<form id="QuoteItemAmountForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('quoteitemamounts_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="quote_item_id">Quote item</label>
    <select name="quote_item_id" id="quote_item_id" class="form-control simple-select">
       <option value="0">Quote item</option>
         <?php foreach ($quote_items as $quote_item) { ?>
          <option value="<?= $quote_item->getId(); ?>"
           <?php $s->check_select(Html::encode($body['quote_item_id'] ?? ''), $quote_item->getId()) ?>
           ><?= $quote_item->getId(); ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <input type="hidden" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
<div class="form-group">
  <label for="subtotal"><?= $s->trans('subtotal'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="subtotal" id="subtotal" class="form-control"
              value="<?= $s->format_amount($body['subtotal'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="tax_total"><?= $s->trans('tax'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="tax_total" id="tax_total" class="form-control"
              value="<?= $s->format_amount($body['tax_total'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="discount"><?= $s->trans('discount'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="discount" id="discount" class="form-control"
              value="<?= $s->format_amount($body['discount'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="total"><?= $s->trans('total'); ?></label>
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
