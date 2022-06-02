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
        <label for="setting_key" class="form-label">Setting Key<span style="color:red">*</span></label>
        <input type="text" class="form-control" name="setting_key" id="setting_key" placeholder="Setting Key" value="<?= Html::encode($body['setting_key'] ?? '') ?>" required>
    </div>
    <div class="mb-3 form-group">
        <label for="setting_value" class="form-label">Setting Value</label>
        <input type="text" class="form-control" name="setting_value" id="setting_value" placeholder="Setting Value" value="<?= Html::encode($body['setting_value'] ?? '') ?>">
    </div>
    <div class="mb-3 form-group">
        <label for="setting_trans" class="form-label">Setting Trans<span style="color:red">*</span></label>
        <input type="text" class="form-control" name="setting_trans" id="setting_trans" placeholder="Setting Trans" value="<?= Html::encode($body['setting_trans'] ?? '') ?>">
    </div>
    <div class="mb-3 form-group">
        <label for="setting_section" class="form-label">Setting Section<span style="color:red">*</span></label>
        <input type="text" class="form-control" name="setting_section" id="setting_section" placeholder="Setting Section" value="<?= Html::encode($body['setting_section'] ?? '') ?>">
    </div>
    <div class="mb-3 form-group">
        <label for="setting_subsection" class="form-label">Setting Subsection<span style="color:red">*</span></label>
        <input type="text" class="form-control" name="setting_subsection" id="setting_subsection" placeholder="Setting Subsection" value="<?= Html::encode($body['setting_subsection'] ?? '') ?>">
    </div>
  </div>    
  <button type="submit" class="btn btn-primary"><?= $s->trans('submit'); ?></button>
</form>
