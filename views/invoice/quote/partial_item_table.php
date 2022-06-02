<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
?>

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
            
            <?php
            //**********************************************************************************************
            // New 
            //**********************************************************************************************
            ?>

            <tbody id="new_row" style="display: none;">
            <tr>
                <td rowspan="2" class="td-icon" style="text-align: center; vertical-align: middle;"><i class="fa fa-arrows"></i></td>
                <td class="td-text">
                    <input type="hidden" name="quote_id" maxlength="7" size="7" value="<?php echo $quote->getId(); ?>">
                    <input type="hidden" name="item_id" maxlength="7" size="7" value="">
                    <input type="hidden" name="item_product_id" maxlength="7" size="7" value="">

                    <div class="input-group">
                        <span class="input-group-text"><?= $s->trans('item'); ?></span>
                        <input type="text" name="item_name" class="input-sm form-control" value="" disabled>
                    </div>
                </td>
                <td class="td-amount td-quantity">
                    <div class="input-group">
                        <span class="input-group-text"><?= $s->trans('quantity'); ?></span>
                        <input type="text" name="item_quantity" class="input-sm form-control amount" value="1.00">
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                        <span class="input-group-text"><?= $s->trans('price'); ?></span>
                        <input type="text" name="item_price" class="input-sm form-control amount" value="0.00">
                    </div>
                </td>
                <td class="td-amount td-vert-middle">
                    <div class="input-group">
                        <span class="input-group-text"><?= $s->trans('item_discount'); ?></span>
                        <input type="text" name="item_discount_amount" class="input-sm form-control amount"
                               data-toggle="tooltip" data-placement="bottom"
                               title="<?= $s->get_setting('currency_symbol') . ' ' . $s->trans('per_item'); ?>" value="0.00">
                    </div>
                </td>
                <td td-vert-middle>
                    <div class="input-group">
                        <span class="input-group-text"><?= $s->trans('tax_rate'); ?></span>
                        <select name="item_tax_rate_id" class="form-control">
                            <option value="0"><?= $s->trans('none'); ?></option>
                            <?php foreach ($tax_rates as $tax_rate) { ?>
                                <option value="<?php echo $tax_rate->getTax_rate_id(); ?>">
                                    <?php echo $numberhelper->format_amount($tax_rate->getTax_rate_percent()) . '% - ' . $tax_rate->getTax_rate_name(); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </td>
                <td class="td-icon text-right td-vert-middle">
                    <form method="POST" class="form-inline">
                            <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                            <button type="submit" class="btn_delete_item btn-xl btn-primary" onclick="return confirm('<?= $s->trans('delete_record_warning'); ?>');">
                                <i class="fa fa-trash"></i>
                            </button>
                    </form>
                </td>
            </tr>
            <tr>
                <td class="td-textarea">
                    <div class="input-group">
                        <span class="input-group-text"><?= $s->trans('description'); ?></span>
                        <textarea name="item_description" class="form-control"></textarea>
                    </div>
                </td>
                <td class="td-amount">
                    <div class="input-group">
                            <span class="input-group-text"><?= $s->trans('product_unit'); ?></span>
                            <select name="item_product_unit_id" class="form-control" disabled>
                                <option value="0"><?= $s->trans('none'); ?></option>
                                <?php foreach ($units as $unit) { ?>
                                    <option value="<?= $unit->getUnit_id(); ?>">
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
            
            <?php
                //*************************************************************************************
                // Current 
                // ************************************************************************************
                $count = 1;
                foreach ($quote_items as $item) { ?>
                <tbody class="item">
                <tr>
                    <td rowspan="2" class="td-icon" style="text-align: center; vertical-align: middle;">
                        <i class="fa fa-arrows"></i>
                        <h5><bold><?= " ".$count; ?></bold></h5>                       
                    </td>
                    <td class="td-text">
                        <div class="input-group">
                            <input type="text" disabled="true" maxlength="4" size="4" name="quote_id" value="<?= $item->getQuote_id(); ?>" data-toggle="tooltip" title="quote_item->quote_id">
                            <input type="text" disabled="true" maxlength="4" size="4" name="item_id" value="<?= $item->getId(); ?>" data-toggle="tooltip" title="quote_item->getId()">
                            <input type="text" disabled="true" maxlength="4" size="4" name="item_product_id" value="<?= $item->getProduct_id(); ?>" data-toggle="tooltip" title="quote_item->product_id">
                        </div>    
                        <div class="input-group">
                            <span class="input-group-text"><?= $s->trans('item'); ?></span>
                            <select name="item_name" class="form-control" disabled>                                
                                <?php foreach ($products as $product) { ?>
                                    <option value="<?php echo $product->getProduct_id(); ?>"
                                            <?php if ($item->getProduct_id() == $product->getProduct_id()) { ?>selected="selected"<?php } ?>>
                                        <?php echo $product->getProduct_name(); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </td>
                    <td class="td-amount td-quantity">
                        <div class="input-group">
                            <span class="input-group-text"><?= $s->trans('quantity'); ?></span>
                            <input disabled type="text" name="item_quantity" class="input-sm form-control amount" data-toggle="tooltip" title="quote_item->quantity"
                                   value="<?= $numberhelper->format_amount($item->getQuantity()); ?>">
                        </div>
                    </td>
                    <td class="td-amount">
                        <div class="input-group">
                            <span class="input-group-text"><?= $s->trans('price'); ?></span>
                            <input disabled type="text" name="item_price" class="input-sm form-control amount" data-toggle="tooltip" title="quote_item->price"
                                   value="<?= $numberhelper->format_amount($item->getPrice()); ?>">
                        </div>
                    </td>
                    <td class="td-amount ">
                        <div class="input-group">
                            <span class="input-group-text"><?= $s->trans('item_discount'); ?></span>
                            <input disabled type="text" name="item_discount_amount" class="input-sm form-control amount" data-toggle="tooltip" title="quote_item->discount_amount"
                                   value="<?= $numberhelper->format_amount($item->getDiscount_amount()); ?>"
                                   data-toggle="tooltip" data-placement="bottom"
                                   title="<?= $s->get_setting('currency_symbol') . ' ' . $s->trans('per_item'); ?>">
                        </div>
                    </td>
                    <td>
                    <?php
                       //get the percentage
                       $percentage = '';
                       foreach ($tax_rates as $tax_rate) {
                       if ($item->getTax_rate_id() == $tax_rate->getTax_rate_id()){
                          $percentage = $numberhelper->format_amount($tax_rate->getTax_rate_percent()) . '% - ' . Html::encode($tax_rate->getTax_rate_name());
                       } 
                    }?>
                        <div class="input-group">
                            <span class="input-group-text"><?= $s->trans('tax_rate'); ?></span>
                            <select disabled name="item_tax_rate_id" class="form-control" data-toggle="tooltip" title="quote_item->tax_rate_id">
                                <?php foreach ($tax_rates as $tax_rate) { ?>
                                    <option value="<?php echo $tax_rate->getTax_rate_id(); ?>"
                                            <?php if ($item->getTax_rate_id() == $tax_rate->getTax_rate_id()) { ?>selected="selected"<?php } ?>>
                                        <?php echo $numberhelper->format_amount($tax_rate->getTax_rate_percent()) . '% - ' . Html::encode($tax_rate->getTax_rate_name()); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </td>
                    <td class="td-icon text-right td-vert-middle">   
                        <a href="<?= $urlGenerator->generate('quote/delete_quote_item',['id'=>$item->getId()]) ?>" class="btn btn-md btn-link" onclick="return confirm('<?= $s->trans('delete_record_warning'); ?>');"><i class="fa fa-trash"></i></a>
                        <a href="<?= $urlGenerator->generate('quoteitem/edit',['id'=>$item->getId()]) ?>" class="btn btn-md btn-link"><i class="fa fa-pencil"></i></a>
                    </td>
                </tr>
                <tr>
                    <td class="td-textarea">
                        <div class="input-group">
                            <span class="input-group-text" data-toggle="tooltip" title="quote_item->description"><?= $s->trans('description'); ?></span>
                            <textarea disabled name="item_description" class="form-control" ><?= Html::encode($item->getDescription()); ?></textarea>
                        </div>
                    </td>
                    <td class="td-amount">
                        <div class="input-group">
                            <span class="input-group-text"><?= $s->trans('product_unit');?></span>
                            <span class="input-group-text" name="item_product_unit"><?= $item->getProduct_unit();?></span>
                        </div>
                    </td>
                    <td class="td-amount td-vert-middle">
                        <span><?= $s->trans('subtotal'); ?></span><br/>                        
                        <span name="subtotal" class="amount" data-toggle="tooltip" title="quote_item_amount->subtotal">
                            <?= $numberhelper->format_currency($quote_item_amount->repoQuoteItemAmountquery((string)$item->getId())->getSubtotal() ?? 0.00); ?>
                        </span>
                    </td>
                    <td class="td-amount td-vert-middle">
                        <span><?= $s->trans('discount'); ?></span><br/>
                        <span name="item_discount_total" class="amount" data-toggle="tooltip" title="quote_item_amount->discount">
                            <?= $numberhelper->format_currency($quote_item_amount->repoQuoteItemAmountquery((string)$item->getId())->getDiscount() ?? 0.00); ?>
                        </span>
                    </td>
                    <td class="td-amount td-vert-middle">
                        <span><?= $s->trans('tax'); ?></span><br/>
                        <span name="item_tax_total" class="amount" data-toggle="tooltip" title="quote_item_amount->tax_total">
                            <?= $numberhelper->format_currency($quote_item_amount->repoQuoteItemAmountquery((string)$item->getId())->getTax_total() ?? 0.00); ?>
                        </span>
                    </td>
                    <td class="td-amount td-vert-middle">
                        <span><?= $s->trans('total'); ?></span><br/>
                        <span name="item_total" class="amount" data-toggle="tooltip" title="quote_item_amount->total">
                            <?= $numberhelper->format_currency($quote_item_amount->repoQuoteItemAmountquery((string)$item->getId())->getTotal() ?? 0.00); ?>
                        </span>
                    </td>                   
                </tr>
                </tbody>
            <?php $count = $count + 1;} ?> 
        </table>
    </div>
     <br>
     
    <div class="row">
        <div class="col-xs-12 col-md-4" quote_tax_rates="<?php $quote_tax_rates; ?>">
           
        </div>
        <div class="col-xs-12 visible-xs visible-sm"><br></div>

        <div class="col-xs-12 col-md-6 col-md-offset-2 col-lg-4 col-lg-offset-4">
            <table class="table table-bordered text-right">
                <tr>
                    <td style="width: 40%;"><?= $s->trans('subtotal'); ?></td>
                    <td style="width: 60%;" class="amount" id="amount_subtotal" data-toggle="tooltip" title="quote_amount->item_subtotal =  quote_item(s)->subtotal - quote_item(s)->discount"><?php echo $numberhelper->format_currency($quote_amount->getItem_subtotal() ?? 0.00); ?></td>
                </tr>
                <tr>
                    <td><?= $s->trans('item_tax'); ?></td>
                    <td class="amount" data-toggle="tooltip" id="amount_item_tax_total" title="quote_amount->item_tax_total"><?php echo $numberhelper->format_currency($quote_amount->getItem_tax_total() ?? 0.00); ?></td>
                </tr>
                <tr>
                    <td><a href="#add-quote-tax" data-toggle="modal" class="btn-xs"><i class="fa fa-plus-circle"></i></a><?= $s->trans('quote_tax'); ?></td>
                    <td>
                        <?php if ($quote_tax_rates) {
                            foreach ($quote_tax_rates as $index => $quote_tax_rate) { ?>
                                <form method="POST" class="form-inline" action="<?= $urlGenerator->generate('quote/delete_quote_tax_rate',['id'=>$quote_tax_rate->getId()]) ?>">
                                    <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                                    <button type="submit" class="btn btn-xs btn-link" onclick="return confirm('<?= $s->trans('delete_tax_warning'); ?>');">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    <span class="text-muted">
                                        <?= Html::encode($quote_tax_rate->getTaxRate()->getTax_rate_name()) . ' ' . $numberhelper->format_amount($quote_tax_rate->getTaxRate()->getTax_rate_percent()) . '%' ?>
                                    </span>
                                    <span class="amount" data-toggle="tooltip" title="quote_tax_rate->quote_tax_rate_amount">
                                        <?php echo $numberhelper->format_currency($quote_tax_rate->getQuote_tax_rate_amount()); ?>
                                    </span>
                                </form>
                            <?php }
                        } else {
                            echo $numberhelper->format_currency('0');
                        } ?>
                    </td>
                </tr>
                <tr>
                    <td class="td-vert-middle"><?= $s->trans('discount'); ?></td>
                    <td class="clearfix">
                        <div class="discount-field">
                            <div class="input-group input-group-sm">
                                <input id="quote_discount_amount" name="quote_discount_amount"
                                       class="discount-option form-control input-sm amount" data-toggle="tooltip" title="quote->discount_amount" disabled
                                       value="<?= $numberhelper->format_amount($quote->getDiscount_amount() != 0 ? $quote->getDiscount_amount() : 0.00); ?>">
                                <div
                                    class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></div>
                            </div>
                        </div>
                        <div class="discount-field">
                            <div class="input-group input-group-sm">
                                <input id="quote_discount_percent" name="quote_discount_percent" data-toggle="tooltip" title="quote->discount_percent" disabled
                                       value="<?= $numberhelper->format_amount($quote->getDiscount_percent() != 0 ? $quote->getDiscount_percent() : 0.00); ?>"
                                       class="discount-option form-control input-sm amount">
                                <div class="input-group-text">&percnt;</div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><b><?= $s->trans('total'); ?></b></td>
                    <td class="amount" id="amount_quote_total" data-toggle="tooltip" title="quote_amount->total"><b><?php echo $numberhelper->format_currency($quote_amount->getTotal() ?? 0.00); ?></b></td>
                </tr>
            </table>
        </div>

    </div>
    <hr>