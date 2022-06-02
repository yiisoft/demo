<?php
    declare(strict_types=1);
?>
<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $s->trans('taxes'); ?>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[default_invoice_tax_rate]">
                                <?= $s->trans('default_invoice_tax_rate'); ?>
                            </label>
                            <?php $body['settings[default_invoice_tax_rate]'] = $s->get_setting('default_invoice_tax_rate');?>
                            <select name="settings[default_invoice_tax_rate]" id="settings[default_invoice_tax_rate]"
                                class="form-control">
                                <option value=""><?= $s->trans('none'); ?></option>
                                <?php foreach ($tax_rates as $tax_rate) { ?>
                                    <option value="<?= $tax_rate->getTax_rate_id(); ?>"
                                        <?php $s->check_select($body['settings[default_invoice_tax_rate]'], $tax_rate->getTax_rate_id()); ?>>
                                        <?= $tax_rate->getTax_rate_percent() . '% - ' . $tax_rate->getTax_rate_name(); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[default_item_tax_rate]">
                                <?= $s->trans('default_item_tax_rate'); ?>
                            </label>                            
                            <?php $body['settings[default_item_tax_rate]'] = $s->get_setting('default_item_tax_rate');?>
                            <select name="settings[default_item_tax_rate]" id="settings[default_item_tax_rate]"
                                class="form-control">
                                <option value=""><?= $s->trans('none'); ?></option>
                                <?php foreach ($tax_rates as $tax_rate) { ?>
                                    <option value="<?= $tax_rate->getTax_rate_id(); ?>"
                                        <?php $s->check_select($body['settings[default_item_tax_rate]'], $tax_rate->getTax_rate_id()); ?>>
                                        <?= $tax_rate->getTax_rate_percent() . '% - ' . $tax_rate->getTax_rate_name(); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[default_include_item_tax]">
                                <?= $s->trans('default_invoice_tax_rate_placement'); ?>
                            </label>
                            <?php $body['settings[default_include_item_tax]'] = $s->get_setting('default_include_item_tax');?>
                            <select name="settings[default_include_item_tax]" id="settings[default_include_item_tax]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value=""><?= $s->trans('none'); ?></option>
                                <option value="0" <?php $s->check_select($body['settings[default_include_item_tax]'], '0'); ?>>
                                    <?= $s->trans('apply_before_item_tax'); ?>
                                </option>
                                <option value="1" <?php $s->check_select($body['settings[default_include_item_tax]'], '1'); ?>>
                                    <?= $s->trans('apply_after_item_tax'); ?>
                                </option>
                            </select>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
