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
<form id="QuoteItemForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('quoteitems_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>true]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group" hidden>
    <label for="quote_tax_rate_id">Tax rate</label>
    <select name="quote_tax_rate_id" id="quote_tax_rate_id" class="form-control">
       <option value="0">Tax rate</option>
         <?php foreach ($quote_tax_rates as $quote_tax_rate) { ?>
          <option value="<?= $quote_tax_rate->getId(); ?>"
           <?php $s->check_select(Html::encode($body['quote_tax_rate_id'] ?? ''), $quote_tax_rate->getId()) ?>
           ><?= $quote_tax_rate->getId(); ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group" hidden>
    <label for="product_id">Product</label>
    <select name="product_id" id="product_id" class="form-control">
       <option value="0">Product</option>
         <?php foreach ($products as $product) { ?>
          <option value="<?= $product->getId(); ?>"
           <?php $s->check_select(Html::encode($body['product_id'] ?? ''), $product->getId()) ?>
           ><?= $product->product_name; ?></option>
         <?php } ?>
    </select>
 </div>
 
 <div class="mb3 form-group" hidden>
    <label for="product_unit_id"><?= $s->trans('item_product_unit');?></label>
    <select name="product_unit_id" id="unit_id" class="form-control">
       <option value="0">Unit</option>
         <?php foreach ($units as $unit) { ?>
          <option value="<?= $unit->getId(); ?>"
           <?php $s->check_select(Html::encode($body['product_unit_id'] ?? ''), $unit->getId()) ?>
           ><?= $unit->unit_name; ?></option>
         <?php } ?>
    </select>
 </div>
    
 <div class="mb-3 form-group has-feedback">
     
 <?php 
        $d = new DateHelper($s);
        $date = $d->getDate($body['date_added'] ?? null);
 ?>
 
<div class="mb3 form-group" hidden>
   <label for="name">Name</label>
   <input type="text" name="name" id="name" class="form-control"
 value="<?= Html::encode($body['name'] ??  ''); ?>">
</div>
<div class="mb3 form-group" hidden>
   <label for="description"><?= $s->trans('description'); ?></label>
   <input type="text" name="description" id="description" class="form-control"
 value="<?= Html::encode($body['description'] ??  ''); ?>">
</div>
<div class="form-group" hidden>
  <label for="quantity"><?= $s->trans('quantity'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="quantity" id="quantity" class="form-control"
              value="<?= $s->format_amount($body['quantity'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group" hidden>
  <label for="price"><?= $s->trans('price'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="price" id="price" class="form-control"
              value="<?= $s->format_amount($body['price'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group" hidden>
      <label for="discount_amount"><?= $s->trans('price'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="discount_amount" id="discount_amount" class="form-control"
              value="<?= $s->format_amount($body['discount_amount'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
 <div class="mb3 form-group" hidden>
   <label for="order"><?= $s->trans('order'); ?></label>
   <input type="text" name="order" id="order" class="form-control"
 value="<?= Html::encode($body['order'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group" hidden>
   <label for="product_unit"><?= $s->trans('product_unit'); ?></label>
   <input type="text" name="product_unit" id="product_unit" class="form-control"
 value="<?= Html::encode($body['product_unit'] ??  ''); ?>">
 </div>

</div>

</div>

</div>

<div class="table-striped">
        <table id="item_table" class="items table-primary table-condensed table-bordered no-margin">
            <thead style="display: none">
            <tr>
                <th></th>
                <th>Item</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Tax Rate</th>
                <th>Subtotal</th>
                <th>Tax</th>
                <th>Total</th>
                <th></th>
            </tr>
            </thead>    
            <tbody class="item">
                <tr>
                    <td rowspan="2" class="td-icon"><i class="fa fa-arrow-right"></i></td>
                    <td class="td-text">
                        <div class="input-group">
                            <span class="input-group-text"><?= $s->trans('item'); ?></span>
                            <select name="product_id" id="product_id" class="form-control">
                               <option value="0">Product</option>
                                 <?php foreach ($products as $product) { ?>
                                  <option value="<?= $product->getId(); ?>"
                                   <?php $s->check_select(Html::encode($body['product_id'] ?? ''), $product->getId()) ?>
                                   ><?= $product->product_name; ?></option>
                                 <?php } ?>
                            </select>
                        </div>
                    </td>
                    <td class="td-amount td-quantity">
                        <div class="input-group">
                            <div class="input-group has-feedback">                                
                                <span class="input-group-text"><?= $s->trans('quantity'); ?></span>
                                <input type="text" name="quantity" id="quantity" class="form-control"
                                    value="<?= $s->format_amount($body['quantity'] ?? ''); ?>">                                
                            </div>
                        </div>
                    </td>
                    <td class="td-amount">
                        <div class="input-group">
                             <div class="input-group has-feedback">
                                 <span class="input-group-text"><?= $s->trans('price'); ?></span>     
                                 <input type="text" name="price" id="price" class="form-control"
                                    value="<?= $s->format_amount($body['price'] ?? ''); ?>">
                                 <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
                             </div>
                        </div>
                    </td>
                    <td class="td-amount ">
                        <div class="input-group">                            
                                <div class="input-group has-feedback">
                                    <span class="input-group-text"><?= $s->trans('item_discount'); ?></span>
                                    <input type="text" name="discount_amount" id="discount_amount" class="form-control"
                                        value="<?= $s->format_amount($body['discount_amount'] ?? ''); ?>">
                                        <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
                                </div>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">                            
                            <span class="input-group-text"><?= $s->trans('tax_rate'); ?></span>
                             <select name="quote_tax_rate_id" id="quote_tax_rate_id" class="form-control">
                                <option value="0"><?= $s->trans('tax_rate'); ?></option>
                                  <?php foreach ($quote_tax_rates as $quote_tax_rate) { ?>
                                   <option value="<?= $quote_tax_rate->getId(); ?>"
                                    <?php $s->check_select(Html::encode($body['quote_tax_rate_id'] ?? ''), $quote_tax_rate->getId()) ?>
                                    ><?= $quote_tax_rate->getId(); ?></option>
                                  <?php } ?>
                             </select>
                        </div>
                    </td>
                    <td class="td-icon text-right td-vert-middle">
                        <button type="button" class="btn_delete_item btn btn-link btn-sm" title="Delete"
                                data-item-id="2">
                            <i class="fa fa-trash text-danger"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td class="td-textarea">
                        <div class="input-group">
                            <span class="input-group-text"><label for="description"><?= $s->trans('description'); ?></label></span>
                            <textarea name="description" class="input-sm form-control"
                            ><?= Html::encode($body['description'] ??  ''); ?></textarea>
                        </div>
                    </td>
                    <td class="td-amount">
                        <div class="input-group">
                            <span class="input-group-text"><?= $s->trans('product_unit'); ?></span>
                            <select name="product_unit_id" id="unit_id" class="form-control">
                               <option value="0">Unit</option>
                                 <?php foreach ($units as $unit) { ?>
                                  <option value="<?= $unit->getId(); ?>"
                                   <?php $s->check_select(Html::encode($body['product_unit_id'] ?? ''), $unit->getId()) ?>
                                   ><?= $unit->unit_name; ?></option>
                                 <?php } ?>
                            </select>                            
                        </div>
                    </td>
                    <td class="td-amount td-vert-middle">
                        <span><?= $s->trans('subtotal'); ?></span><br/>                        
                        <span name="subtotal" class="amount"></span>
                    </td>
                    <td class="td-amount td-vert-middle">
                        <span><?= $s->trans('discount'); ?></span><br/>
                        <span name="item_discount_total" class="amount"></span>
                    </td>
                    <td class="td-amount td-vert-middle">
                        <span><?= $s->trans('tax'); ?></span><br/>
                        <span name="item_tax_total" class="amount"></span>
                    </td>
                    <td class="td-amount td-vert-middle">
                        <span><?= $s->trans('total'); ?></span><br/>
                        <span name="item_total" class="amount"></span>
                    </td>
                </tr>
            </tbody>
</table>
</form>
</div>