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
<form id="QuoteItemForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('quoteitems_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="tax_rate_id">Tax rate</label>
    <select name="tax_rate_id" id="tax_rate_id" class="form-control simple-select">
       <option value="0">Tax rate</option>
         <?php foreach ($tax_rates as $tax_rate) { ?>
          <option value="<?= $tax_rate->id; ?>"
           <?php $s->check_select(Html::encode($body['tax_rate_id'] ?? ''), $tax_rate->id) ?>
           ><?= $tax_rate->id; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
    <label for="product_id">Product</label>
    <select name="product_id" id="product_id" class="form-control simple-select">
       <option value="0">Product</option>
         <?php foreach ($products as $product) { ?>
          <option value="<?= $product->id; ?>"
           <?php $s->check_select(Html::encode($body['product_id'] ?? ''), $product->id) ?>
           ><?= $product->product_name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
    <label for="quote_id">Quote</label>
    <select name="quote_id" id="quote_id" class="form-control simple-select">
       <option value="0">Quote</option>
         <?php foreach ($quotes as $quote) { ?>
          <option value="<?= $quote->id; ?>"
           <?php $s->check_select(Html::encode($body['quote_id'] ?? ''), $quote->id) ?>
           ><?= $quote->id; ?></option>
         <?php } ?>
    </select>
 </div>
 
 <div class="mb3 form-group">
    <label for="product_unit_id">Product Unit</label>
    <select name="product_unit_id" id="unit_id" class="form-control simple-select">
       <option value="0">Unit</option>
         <?php foreach ($units as $unit) { ?>
          <option value="<?= $unit->id; ?>"
           <?php $s->check_select(Html::encode($body['product_unit_id'] ?? ''), $unit->id) ?>
           ><?= $unit->unit_name; ?></option>
         <?php } ?>
    </select>
 </div>
    
 <div class="mb3 form-group">
   <input type="hidden" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
 <div class="mb-3 form-group has-feedback"> <?php  $date = $body['date_added'] ?? null; 
$datehelper = new DateHelper(); 
if ($date && $date !== "0000-00-00") { 
    $date = $datehelper->date_from_mysql($date, false, $s); 
} else { 
    $date = null; 
} 
   ?>  
<label form-label for="date_added"><?= "Date Created (".  $datehelper->date_format_datepicker($s).") "; ?></label><div class="mb3 input-group"> 
<input type="text" name="date_added" id="date_added" placeholder="<?= $datehelper->date_format_datepicker($s); ?>" 
       class="form-control data-datepicker" 
       value="<?php if ($date <> null) {echo Html::encode($date);} ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div>   <div class="mb3 form-group">
   <label for="name"><?= $s->trans('name'); ?></label>
   <input type="text" name="name" id="name" class="form-control"
 value="<?= Html::encode($body['name'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="description"><?= $s->trans('description'); ?></label>
   <input type="text" name="description" id="description" class="form-control"
 value="<?= Html::encode($body['description'] ??  ''); ?>">
 </div>
<div class="form-group">
  <label for="quantity"><?= $s->trans('quantity'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="quantity" id="quantity" class="form-control"
              value="<?= $s->format_amount($body['quantity'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="price"><?= $s->trans('price'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="price" id="price" class="form-control"
              value="<?= $s->format_amount($body['price'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="discount_amount">Discount</label>
      <div class="input-group has-feedback">
          <input type="text" name="discount_amount" id="discount_amount" class="form-control"
              value="<?= $s->format_amount($body['discount_amount'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
 <div class="mb3 form-group">
   <label for="order"><?= $s->trans('order'); ?></label>
   <input type="text" name="order" id="order" class="form-control"
 value="<?= Html::encode($body['order'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="product_unit"><?= $s->trans('product_unit'); ?></label>
   <input type="text" name="product_unit" id="product_unit" class="form-control"
 value="<?= Html::encode($body['product_unit'] ??  ''); ?>">
 </div>

</div>

</div>

</div>
</form>
