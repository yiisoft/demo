<?php
  declare(strict_types=1); 
  use App\Invoice\Helpers\NumberHelper;  
  $numberhelper = new NumberHelper($s);
?>

<div id="modal-choose-items" class="modal modal-lg" role="dialog" aria-labelledby="modal_choose_items" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label><?= $s->trans('any_family'); ?></label>
                <div class="form-group">
                    <select name="filter_family_inv" id="filter_family_inv" class="form-control">
                        <option value="0"><?= $s->trans('any_family'); ?></option>
                        <?php foreach ($families as $family) { ?>
                            <option value="<?= $family->getFamily_id(); ?>"
                                <?php if (isset($filter_family) && $family->getFamily_id() == $filter_family) {
                                    echo ' selected="selected"';
                                } ?>>
                                <?= $family->getFamily_name(); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group panel panel-primary">
                    <label><?= $s->trans('product_name'); ?></label>
                    <input type="text" class="form-control" name="filter_product_inv" id="filter_product_inv"
                           placeholder="<?= $s->trans('product_name'); ?>"
                           value="<?= $filter_product ?>">                
                    <button type="button" id="filter-button-inv" class="btn btn-info"><?= $s->trans('search_product'); ?></button>
                    <button type="button" id="product-reset-button-inv" class="btn btn-danger"><?= $s->trans('reset'); ?></button>
                </div>
            </div>

            <br/>

            <div id="product-lookup-table">
                <?php  
                    $response = $head->renderPartial('invoice/product/_partial_product_table_modal',[
                        's'=>$s, 
                        'products'=>$products,
                        'numberhelper'=>$numberhelper,
                    ]);
                    echo (string)$response->getBody();
                ?>     
            </div>
        </div>
        <div class="modal-header">                             
                <button class="select-items-confirm-inv btn btn-success alignment:center" type="button" disabled>
                    <i class="fa fa-check"></i>
                    <?= $s->trans('submit'); ?>
                </button>            
        </div>
    </form>
    <div id="default_item_tax_rate" value="<?= $default_item_tax_rate; ?>"></div>
</div>