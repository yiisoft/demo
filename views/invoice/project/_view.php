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
   <label for="project_name" class="form-label" style="background:lightblue"><?= $s->trans('project_name'); ?></label>
   <?= Html::encode($body['project_name'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="client_id" class="form-label" style="background:lightblue"><?= $s->trans('client'); ?></label>
   <?= $project->getClient()->client_name;?>
 </div>
</div>
