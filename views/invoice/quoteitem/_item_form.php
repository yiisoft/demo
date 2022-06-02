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
<div class="panel panel-default">
<div class="panel-heading">
        <?= $s->trans('item'); ?>
</div>
<form id="QuoteItemForm" method="POST" action="<?= $urlGenerator->generate(...$action)?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div class="table-striped">
<table id="item_table" class="items table-primary table table-bordered no-margin">
<thead style="display: none">
<tr>
    <th></th>
    <th><?= $s->trans('item'); ?></th>
    <th><?= $s->trans('description'); ?></th>
    <th><?= $s->trans('quantity'); ?></th>
    <th><?= $s->trans('price'); ?></th>
    <th><?= $s->trans('tax_rate'); ?></th>
    <th><?= $s->trans('subtotal'); ?></th>
    <th><?= $s->trans('tax'); ?></th>
    <th><?= $s->trans('total'); ?></th>
    <th></th>
</tr>
</thead>
<tbody id="new_quote_item_row">
            <tr>
                <td rowspan="2" class="td-icon"><i class="fa fa-arrows cursor-move"></i></td>
                <td class="td-text">
                    <input type="hidden" name="quote_id" maxlength="7" size="7" value="<?= Html::encode($body['quote_id'] ??  ''); ?>">
                    <input type="hidden" name="id" maxlength="7" size="7" value="<?= Html::encode($body['id'] ??  ''); ?>">
                    <input type="hidden" name="name" value="<?= Html::encode($body['name'] ??  ''); ?>">
                    <input type="hidden" name="order" id="order" value="<?= Html::encode($body['order'] ?? ''); ?>">
                    <div class="input-group">
                            <span class="input-group-text"><?= $s->trans('item'); ?></span>
                            
                            <select name="product_id" id="product_id" class="form-control has-feedback" required>
                                 <!-- avoid using a zero option here -->
                                 <?php foreach ($products as $product) { ?>
                                  <option value="<?= $product->getProduct_id() ?? ''; ?>"
                                   <?php $s->check_select(Html::encode($body['product_id'] ?? ''), $product->getProduct_id()) ?>
                                   ><?= $product->getProduct_name(); ?></option>
                                 <?php } ?>
                            </select>
                    </div>
                </td>
                <td class="td-amount td-quantity">
                    <div class="input-group">
                        <span class="input-group-text"><?= $s->trans('quantity'); ?></span>
                        <input type="number" name="quantity" class="input-sm form-control amount has-feedback" required value="<?= $numberhelper->format_amount($body['quantity'] ?? ''); ?>">
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                        <span class="input-group-text"><?= $s->trans('price'); ?></span>
                        <input type="number" name="price" class="input-sm form-control amount has-feedback" required value="<?= $numberhelper->format_amount($body['price'] ?? ''); ?>">
                    </div>
                </td>
                <td class="td-amount td-vert-middle">
                    <div class="input-group">
                        <span class="input-group-text"><?= $s->trans('item_discount'); ?></span>
                        <input type="number" name="discount_amount" class="input-sm form-control amount has-feedback" required
                               data-toggle="tooltip" data-placement="bottom"
                               title="<?= $s->get_setting('currency_symbol') . ' ' . $s->trans('per_item'); ?>" value="<?= $numberhelper->format_amount($body['discount_amount'] ?? ''); ?>">
                    </div>
                </td>
                <td td-vert-middle>
                    <div class="input-group">
                        <span class="input-group-text"><?= $s->trans('tax_rate'); ?></span>
                        <select name="tax_rate_id" class="form-control amount has-feedback" required> 
                            <!-- avoid using a zero option here -->
                            <?php foreach ($tax_rates as $tax_rate) { ?>
                                <option value="<?php echo $tax_rate->getTax_rate_id(); ?>" <?php $s->check_select(Html::encode($body['tax_rate_id'] ?? ''), $tax_rate->getTax_rate_id()) ?>>
                                    <?php echo $numberhelper->format_amount($tax_rate->getTax_rate_percent()) . '% - ' . $tax_rate->getTax_rate_name(); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
                <td class="td-icon text-right td-vert-middle">
                     <!-- see line 1148 QuoteController: id modal-choose-items lies on views/product/modal_product_lookups_quote.php-->
                    <button class="btn btn btn-primary" href="#modal-choose-items" id="modal-choose-items" data-toggle="modal"><i class="fa fa-list"></i></button>
                    <button type="submit" class="btn btn btn-info" data-toggle="tooltip" title="quoteitem/add"><i class="fa fa-plus"></i><?= $s->trans('save'); ?></button>
                </td>              
            </tr>
            <tr>
                <td class="td-textarea">
                    <div class="input-group">
                        <span class="input-group-text"><?= $s->trans('description'); ?></span>
                        <textarea name="description" class="form-control"><?= Html::encode($body['description'] ??  ''); ?></textarea>
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                            <span class="input-group-text"><?= $s->trans('product_unit'); ?></span>
                            <select name="product_unit_id" class="form-control amount has-feedback" required>
                                <!-- avoid using a zero option here -->
                                <?php foreach ($units as $unit) { ?>
                                    <option value="<?= $unit->getUnit_id(); ?>" <?php $s->check_select(Html::encode($body['product_unit_id'] ?? ''), $unit->getUnit_id()) ?>>
                                        <?= Html::encode($unit->getUnit_name()) . "/" . Html::encode($unit->getUnit_name_plrl()); ?>
                                    </option>
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
                    <span name="discount_total" class="amount"></span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?= $s->trans('tax'); ?></span><br/>
                    <span name="tax_total" class="amount"></span>
                </td>
                <td class="td-amount td-vert-middle">
                    <span><?= $s->trans('total'); ?></span><br/>
                    <span name="total" class="amount"></span>
                </td>
            </tr>
</tbody>
</table>
</div>
<div class="col-xs-12 col-md-4">           
            <div class="btn-group">
               <button hidden class="btn_quote_item_add_row btn btn-primary btn-md active"><i class="fa fa-plus"></i><?php echo $s->trans('add_new_row'); ?></button>                              
            </div>           
</div>
</form>
<br>
<br>
</div>