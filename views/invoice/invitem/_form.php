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
<form id="ItemForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('items_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="inv_id"><?= $s->trans('invoices'); ?></label>
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
    <label for="tax_rate_id"><?= $s->trans('tax_rates'); ?></label>
    <select name="tax_rate_id" id="tax_rate_id" class="form-control simple-select">
       <option value="0">Tax rate</option>
         <?php foreach ($tax_rates as $tax_rate) { ?>
          <option value="<?= $tax_rate->getId(); ?>"
           <?php $s->check_select(Html::encode($body['tax_rate_id'] ?? ''), $tax_rate->getId()) ?>
           ><?= $tax_rate->tax_rate_name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
    <label for="product_id"><?= $s->trans('products'); ?></label>
    <select name="product_id" id="product_id" class="form-control simple-select">
       <option value="0">Product</option>
         <?php foreach ($products as $product) { ?>
          <option value="<?= $product->getId(); ?>"
           <?php $s->check_select(Html::encode($body['product_id'] ?? ''), $product->getId()) ?>
           ><?= $product->product_name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
    <label for="unit_id"><?= $s->trans('units'); ?></label>
    <select name="unit_id" id="unit_id" class="form-control simple-select">
       <option value="0">Unit</option>
         <?php foreach ($units as $unit) { ?>
          <option value="<?= $unit->getId(); ?>"
           <?php $s->check_select(Html::encode($body['unit_id'] ?? ''), $unit->getId()) ?>
           ><?= $unit->unit_name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
    <label for="task_id"><?= $s->trans('tasks'); ?></label>
    <select name="task_id" id="task_id" class="form-control simple-select">
       <option value="0">Task</option>
         <?php foreach ($tasks as $task) { ?>
          <option value="<?= $task->getId(); ?>"
           <?php $s->check_select(Html::encode($body['task_id'] ?? ''), $task->getId()) ?>
           ><?= $task->task_name; ?></option>
         <?php } ?>
    </select>
 </div> 
 <div class="mb-3 form-group has-feedback"> <label form-label for="date_added"><?= $s->trans('date_created'); ?></label>
<?php  $date_add = $body['date_added'] ?? null; 
$datehelper = new DateHelper($s); 
if ($date_add && $date_add !== "0000-00-00") { 
    $date_add = $datehelper->date_from_mysql($date_add); 
} else { 
    $date_add = null; 
} 
   ?>  
<div class="mb3 input-group">
<input type="text" name="date_added" id="date_added" placeholder="<?= $datehelper->display(); ?>" 
       class="form-control data-datepicker" value="<?php if ($date_add <> null) {echo Html::encode($date_add);} ?>"> 
<span class="input-group-text"><i class="fa fa-calendar fa-fw"></i></span> 
</div> 
     
</div>   
<div class="mb3 form-group">
   <label for="name"><?= $s->trans('item_name'); ?></label>
   <input type="text" name="name" id="name" class="form-control" value="<?= Html::encode($body['name'] ??  ''); ?>">
</div>
<div class="mb3 form-group">
   <label for="description"><?= $s->trans('description'); ?></label>
   <input type="text" name="description" id="description" class="form-control"
 value="<?= Html::encode($body['description'] ??  ''); ?>">
 </div>
<div class="input-group">
  <label for="quantity"><?= $s->trans('quantity'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="quantity" id="quantity" class="form-control"
              value="<?= $s->format_amount($body['quantity'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="input-group">
  <label for="price"><?= $s->trans('price'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="price" id="price" class="form-control"
              value="<?= $s->format_amount($body['price'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="input-group">
  <label for="discount_amount"><?= $s->trans('item_discount'); ?></label>
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
   <label for="product_unit"><?= $s->trans('units'); ?></label>
   <input type="text" name="product_unit" id="unit" class="form-control"
 value="<?= Html::encode($body['product_unit'] ??  ''); ?>">
 </div>
 <div class="mb-3 form-group has-feedback"> <label form-label for="date"><?= $s->trans('date'); ?></label><?php  $ddate = $body['date'] ?? null; 
$datehelper = new DateHelper($s); 
if ($ddate && $ddate !== "0000-00-00") { 
    $ddate = $datehelper->date_from_mysql($ddate); 
} else { 
    $ddate = null; 
} 
   ?>  
<div class="mb3 input-group">   
<input type="text" name="date" id="date" placeholder="<?= $datehelper->display(); ?>" class="form-control data-datepicker" 
       value="<?php 
                    echo Html::encode($ddate); 
              ?>"> 
<span class="input-group-text"><i class="fa fa-calendar fa-fw"></i></span>
</div>   
</div> 
</div>  
</div>
</div>
</div>
</form>
