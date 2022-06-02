<?php

declare(strict_types=1); 

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 * @var string $csrf
 */

if (!empty($errors)) {
    foreach ($errors as $field => $error) {
        echo Alert::widget()->options(['class' => 'alert-danger'])->body(Html::encode($field . ':' . $error));
    }
}
?>

<form method="post">

    <input type="hidden" name="_csrf" value="<?= $csrf ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?= $s->trans('custom_field_form'); ?></h1>
        <?php echo $layout_header_buttons; ?>
    </div>

    <div id="content" class="row">

        <div class="col-xs-12 col-md-6 col-md-offset-3">

            <div class="form-group">
                <label for="table"><?= $s->trans('table'); ?></label>
                <select name="table" id="table" class="form-control">
                    <?php foreach ($tables as $table => $label) { ?>
                        <option value="<?= $table; ?>"
                            <?php $s->check_select(Html::encode($body['table'] ??  ''), $table); ?>>
                            <?= $s->lang($label); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="label"><?= $s->trans('label'); ?></label>
                <input type="text" name="label" id="label" class="form-control"
                       value="<?= Html::encode($body['label'] ??  ''); ?>">
            </div>

            <div class="form-group">
                <label for="type"><?= $s->trans('type'); ?></label>
                <select name="type" id="type" class="form-control">
                    <?php
                        $helper = new ArrayHelper();
                        $arrays = [$user_input_types, $custom_value_fields];
                        $types = $helper->merge(...$arrays);
                        foreach ($types as $type) { ?>
                        <?php $alpha = str_replace("-", "_", strtolower($type)); ?>
                        <option value="<?= $type; ?>"
                            <?php $s->check_select(Html::encode($body['type'] ??  '')); ?>>
                            <!-- the 'number' input field has been added to the type of custom fields that can be added -->
                            <?= null!== $s->trans($alpha) ? $s->trans($alpha) : $translator->translate('invoice.custom.field.number'); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="order" class="form-label"><?= $s->trans('order'); ?></label>
                <input type="range" min= "1" max="20" class="form-range" name="order" id="order" class="form-control" value="<?= Html::encode($body['order'] ??  ''); ?>">
            </div>

            <div class="form-group">
                <label for="location"><?= $s->trans('position'); ?></label> 
                <?php $valueSelected = Html::encode($body['location'] ??  ''); ?>
                <select name="location" id="location" class="form-control"></select>
            </div>

        </div>

    </div>
    <?php
    // double dropdown box
    $js2 = "$(function () {"."\n".
           "var jsonPositions ='".$positions."';"."\n".
           "jsonPositions = JSON.parse(jsonPositions);"."\n". 
           "function updatePositions(index, selKey) {"."\n".
                '$("#location option").remove();'."\n".
                "var pos = 0;"."\n".
                "var key = Object".'.'.'keys(jsonPositions)[index];'."\n".
                'for (pos in jsonPositions[key]) {'."\n".
                   'var opt = $("<'."option".'>");'."\n".
                   'opt.attr("value", pos);'."\n".
                   'opt.text(jsonPositions[key][pos]);'."\n".
                   'if (selKey == pos) {'."\n".
                      'opt.attr("selected", "selected");'."\n".
                   "}"."\n".
                   '$("#location").append(opt);'."\n".
                '}'."\n".
            "}"."\n".
            'var optionIndex = $("#table option:selected").index();'."\n".
            '$("#table").on("change", function () {'."\n".
            'optionIndex = $("#table option:selected").index();'."\n".
            'updatePositions(optionIndex);'."\n".
            '});'."\n".
            'updatePositions(optionIndex,'. $valueSelected. ');'.
            '});';
    echo Html::script($js2)->type('module');
?> 

</form>

