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
<label for="start" class="form-label" style="background:lightblue"><?= $s->trans('start'); ?></label>
   <?= Html::encode($body['start'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="end" class="form-label" style="background:lightblue"><?= $s->trans('end'); ?></label>
   <?= Html::encode($body['end'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="frequency" class="form-label" style="background:lightblue"><?= $s->trans('frequency'); ?></label>
   <?= Html::encode($body['frequency'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="next" class="form-label" style="background:lightblue"><?= $s->trans('next'); ?></label>
   <?= Html::encode($body['next'] ?? ''); ?>
 </div>
</div>
