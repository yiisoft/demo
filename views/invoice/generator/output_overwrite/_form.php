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
<form id="ProductForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title">ProductForm</h1>
<div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="tax_rate_id">Tax rate</label>
    <select name="tax_rate_id" id="tax_rate_id" class="form-control simple-select">
       <option value="0">Tax rate</option>
         <?php foreach ($tax_rates as $tax_rate) { ?>
          <option value="<?= $tax_rate->id; ?>"
           <?php $s->check_select(Html::encode($body['tax_rate_id'] ?? ''), $tax_rate->id) ?>
           ><?= $tax_rate->tax_rate_name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
    <label for="family_id">Family</label>
    <select name="family_id" id="family_id" class="form-control simple-select">
       <option value="0">Family</option>
         <?php foreach ($familys as $family) { ?>
          <option value="<?= $family->id; ?>"
           <?php $s->check_select(Html::encode($body['family_id'] ?? ''), $family->id) ?>
           ><?= $family->family_name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
    <label for="unit_id">Unit</label>
    <select name="unit_id" id="unit_id" class="form-control simple-select">
       <option value="0">Unit</option>
         <?php foreach ($units as $unit) { ?>
          <option value="<?= $unit->id; ?>"
           <?php $s->check_select(Html::encode($body['unit_id'] ?? ''), $unit->id) ?>
           ><?= $unit->unit_name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <label for="id">id</label>
   <input type="text" name="id" id="id" class="form-control"
      value="<?= Html::encode($body['id'] ?? ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="product_sku">product_sku</label>
   <input type="text" name="product_sku" id="product_sku" class="form-control"
      value="<?= Html::encode($body['product_sku'] ?? ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="product_name">product_name</label>
   <input type="text" name="product_name" id="product_name" class="form-control"
      value="<?= Html::encode($body['product_name'] ?? ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="product_description">product_description</label>
   <input type="text" name="product_description" id="product_description" class="form-control"
      value="<?= Html::encode($body['product_description'] ?? ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="product_price">product_price</label>
   <input type="text" name="product_price" id="product_price" class="form-control"
      value="<?= Html::encode($body['product_price'] ?? ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="purchase_price">purchase_price</label>
   <input type="text" name="purchase_price" id="purchase_price" class="form-control"
      value="<?= Html::encode($body['purchase_price'] ?? ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="provider_name">provider_name</label>
   <input type="text" name="provider_name" id="provider_name" class="form-control"
      value="<?= Html::encode($body['provider_name'] ?? ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="product_tariff">product_tariff</label>
   <input type="text" name="product_tariff" id="product_tariff" class="form-control"
      value="<?= Html::encode($body['product_tariff'] ?? ''); ?>">
 </div>
</div>
</form>
