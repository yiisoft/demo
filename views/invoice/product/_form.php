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

<form id="productForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data" >

    <input type="hidden" name="_csrf" value="<?= $csrf ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?= $s->trans('products_form'); ?></h1>
        <?php
            $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]);
            echo (string)$response->getBody();
        ?>
        <div class="mb-3 form-group">
    </div>
    </div>

    <div id="content">

        <div class="row">
            <div class="mb-3 form-group">

                <div class="panel panel-default">
                    <div class="panel-heading">

                        <?php if (!empty($body['product_id'])) : ?>
                            #<?php echo Html::encode($body['product_id'] ?? ''); ?>&nbsp;
                            <?php echo Html::encode($body['product_name'] ?? ''); ?>
                        <?php else : ?>
                            <?= $s->trans('new_product'); ?>
                        <?php endif; ?>

                    </div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="family_id">
                                <?= $s->trans('family'); ?>
                            </label>
                            <select name="family_id" id="family_id" class="form-control simple-select">
                                <option value="0"><?= $s->trans('select_family'); ?></option>
                                <?php foreach ($families as $family) { ?>
                                    <option value="<?= $family->id; ?>"
                                        <?php $s->check_select(Html::encode($body['family_id'] ?? ''), $family->id) ?>
                                    ><?= $family->family_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="product_sku">
                                <?= $s->trans('product_sku'); ?>
                            </label>

                            <input type="text" name="product_sku" id="product_sku" class="form-control"
                                   value="<?= Html::encode($body['product_sku'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="product_name">
                                <?= $s->trans('product_name'); ?>
                            </label>

                            <input type="text" name="product_name" id="product_name" class="form-control" required
                                   value="<?= Html::encode($body['product_name'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="product_description">
                                <?= $s->trans('product_description'); ?>
                            </label>

                            <textarea name="product_description" id="product_description" class="form-control"
                                      rows="3"><?= Html::encode($body['product_description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="product_price">
                                <?= $s->trans('product_price'); ?>
                            </label>

                            <div class="input-group has-feedback">
                                <input type="text" name="product_price" id="product_price" class="form-control"
                                       value="<?= $s->format_amount($body['product_price'] ?? ''); ?>">
                                <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="unit_id">
                                <?= $s->trans('product_unit'); ?>
                            </label>

                            <select name="unit_id" id="unit_id" class="form-control simple-select">
                                <option value="0"><?= $s->trans('select_unit'); ?></option>
                                <?php foreach ($units as $unit) { ?>
                                    <option value="<?= $unit->id; ?>"
                                        <?php $s->check_select(Html::encode($body['unit_id'] ?? ''), $unit->id); ?>
                                    ><?= $unit->unit_name . '/' . $unit->unit_name_plrl; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tax_rate_id">
                                <?= $s->trans('tax_rate'); ?>
                            </label>

                            <select name="tax_rate_id" id="tax_rate_id" class="form-control simple-select">
                                <option value="0"><?= $s->trans('none'); ?></option>
                                <?php foreach ($tax_rates as $tax_rate) { ?>
                                    <option value="<?= $tax_rate->id; ?>"
                                        <?= $s->check_select(Html::encode($body['tax_rate_id'] ?? ''), $tax_rate->id); ?>>
                                        <?= $tax_rate->tax_rate_name
                                            . ' (' . $s->format_amount($tax_rate->tax_rate_percent) . '%)'; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-md-6">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= $s->trans('extra_information'); ?>
                    </div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="provider_name">
                                <?= $s->trans('provider_name'); ?>
                            </label>

                            <input type="text" name="provider_name" id="provider_name" class="form-control"
                                   value="<?= Html::encode($body['provider_name'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="purchase_price">
                                <?= $s->trans('purchase_price'); ?>
                            </label>

                            <div class="input-group has-feedback">
                                <input type="text" name="purchase_price" id="purchase_price" class="form-control"
                                       value="<?= $s->format_amount($body['purchase_price'] ?? ''); ?>">
                                <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= $s->trans('invoice_sumex'); ?>
                    </div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label for="product_tariff">
                                <?= $s->trans('product_tariff'); ?>
                            </label>

                            <input type="text" name="product_tariff" id="product_tariff" class="form-control"
                                   value="<?= Html::encode($body['product_tariff'] ?? ''); ?>">
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>

</form>
