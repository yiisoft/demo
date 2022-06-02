<?php
  declare(strict_types=1); 
  
  use Yiisoft\Html\Html;
  use App\Invoice\Helpers\NumberHelper;
  
  $numberhelper = new NumberHelper($s);
  
  // id="add-quote-tax" triggered by <a href="#add-quote-tax" data-toggle="modal"  style="text-decoration:none"> on views/quote/view.php line 67
?>

<div id="add-quote-tax" class="modal modal-lg" role="dialog" aria-labelledby="modal_add_quote_tax" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></button>
        </div>
        <div class="modal-body">
            <div class="mb3 form-group">
                <h6><?= $s->trans('add_quote_tax'); ?></h6>
            </div>
            <div class="mb3 form-group">
                <label for="tax_rate_id">
                    <?= $s->trans('tax_rate'); ?>
                </label>
                <div>
                    <select name="tax_rate_id" id="tax_rate_id" class="form-control" required>
                        <?php foreach ($tax_rates as $tax_rate) { ?>
                            <option value="<?= $tax_rate->getTax_rate_id(); ?>">
                                <?= $numberhelper->format_amount($tax_rate->getTax_rate_percent()) . '% - ' . Html::encode($tax_rate->getTax_rate_name()); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="mb3 form-group">
                <label for="include_item_tax">
                    <?= $s->trans('tax_rate_placement'); ?>
                </label>

                <div>
                    <select name="include_item_tax" id="include_item_tax" class="form-control">
                        <option value="0">
                            <?php echo $s->trans('apply_before_item_tax'); ?>
                        </option>
                        <option value="1">
                            <?php echo $s->trans('apply_after_item_tax'); ?>
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="quote_tax_submit btn btn-success" id="quote_tax_submit" type="button">
                    <i class="fa fa-check"></i><?= $s->trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= $s->trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
