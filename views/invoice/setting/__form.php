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

<form id="settingForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
  <div class="row">
    <div class="mb-3 form-group">
        <label for="setting_key" class="form-label">Setting Key</label>
        <input type="text" class="form-control" name="setting_key" id="setting_key" placeholder="Setting Key" value="<?= Html::encode($body['setting_key'] ?? '') ?>" required>
    </div>
    <div class="mb-3 form-group">
        <label for="setting_value" class="form-label">Setting Value</label>
        <input type="text" class="form-control" name="setting_value" id="setting_value" placeholder="Setting Value" value="<?= Html::encode($body['setting_value'] ?? '') ?>">
    </div>      
  </div>    
  <button type="submit" class="btn btn-primary"><?= $s->trans('submit'); ?></button>
</form>
