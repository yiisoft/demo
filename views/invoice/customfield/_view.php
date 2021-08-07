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
<div class="row">
 <div class="mb3 form-group">
<label for="id" class="form-label" style="background:lightblue"><?= $s->trans('id'); ?></label>
   <?= Html::encode($body['id'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="table" class="form-label" style="background:lightblue"><?= $s->trans('table'); ?></label>
   <?= Html::encode($body['table'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="label" class="form-label" style="background:lightblue"><?= $s->trans('label'); ?></label>
   <?= Html::encode($body['label'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="type" class="form-label" style="background:lightblue"><?= $s->trans('type'); ?></label>
   <?= Html::encode($body['type'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="location" class="form-label" style="background:lightblue">Location</label>
   <?= Html::encode($body['location'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="order" class="form-label" style="background:lightblue"><?= $s->trans('order'); ?></label>
   <?= Html::encode($body['order'] ?? ''); ?>
 </div>
</div>
