<?php

declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;

/**
 * @var \Yiisoft\View\View $this
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var string $title
 */
?>
<form method="post">

    <input type="hidden" name="_csrf" value="<?= $csrf; ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?= $s->trans('custom_values_edit'); ?></h1>
        <?= $header_buttons; ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">
                <?php 
                    if (!empty($errors)) {
                        foreach ($errors as $field => $error) {
                            echo Alert::widget()->options(['class' => 'alert-danger'])->body(Html::encode($field . ':' . $error));
                        }
                    } 
                ?>
                
                <?php $alpha = str_replace("-", "_", strtolower($custom_field->type)); ?>
                
                <div class="mb3 form-group">
                    <input hidden type="hidden" name="custom_field_id" id="custom_field_id" class="form-control"  value="<?= Html::encode($body['custom_field_id']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="type"><?= $s->trans('type'); ?></label>
                    <input type="text" class="form-control" id="type" value="<?= Html::encode($s->trans($alpha)); ?>" disabled="disabled"/>
                </div>

                <div class="form-group">
                    <label for="value"><?= $s->trans('label'); ?></label>
                    <input type="text" name="value" id="value" class="form-control" value="<?= Html::encode($body['value'] ??  ''); ?>">
                </div>
            </div>
        </div>

    </div>

</form>
