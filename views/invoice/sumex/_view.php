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
   <label for="invoice" class="form-label" style="background:lightblue"><?= $s->trans('invoice'); ?></label>
   <?= Html::encode($body['invoice'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="reason" class="form-label" style="background:lightblue"><?= $s->trans('reason'); ?></label>
   <?= Html::encode($body['reason'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="diagnosis" class="form-label" style="background:lightblue"><?= $s->trans('diagnosis'); ?></label>
   <?= Html::encode($body['diagnosis'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="observations" class="form-label" style="background:lightblue"><?= $s->trans('observations'); ?></label>
   <?= Html::encode($body['observations'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="treatmentstart" class="form-label" style="background:lightblue"><?= $s->trans('treatmentstart'); ?></label>
   <?= Html::encode($body['treatmentstart'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="treatmentend" class="form-label" style="background:lightblue"><?= $s->trans('treatmentend'); ?></label>
   <?= Html::encode($body['treatmentend'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="casedate" class="form-label" style="background:lightblue"><?= $s->trans('casedate'); ?></label>
   <?= Html::encode($body['casedate'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="casenumber" class="form-label" style="background:lightblue"><?= $s->trans('casenumber'); ?></label>
   <?= Html::encode($body['casenumber'] ?? ''); ?>
 </div>
</div>
