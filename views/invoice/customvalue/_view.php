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
<h1><?= Html::encode($title) ?></h1>
<div class="row">
 <div class="mb3 form-group">
<label for="value" class="form-label" style="background:lightblue"><?= $s->trans('value'); ?></label>
   <?= Html::encode($body['value'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="custom_field_id" class="form-label" style="background:lightblue"><?= $s->trans('custom_field'); ?></label>
   <?= $customvalue->getCustomField()->getId();?>
 </div>
</div>
