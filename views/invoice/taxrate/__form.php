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

<form id="taxrateForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
  <div class="row">
    <div class="mb-3 form-group">
        <input type="text" class="form-control" name="tax_rate_name" id="tax_rate_name" placeholder="Tax Rate Name" value="<?= Html::encode($body['tax_rate_name'] ?? '') ?>" required>
    </div>
    <div class="mb-3 form-group">
        <input type="text" class="form-control" name="tax_rate_percent" id="tax_rate_percent" placeholder="Tax Rate Percent" value="<?= $s->format_amount($body['tax_rate_percent'] ?? 0) ?>" required>
        <span class="form-control-feedback">%</span>
    </div>           
  </div>    
  <button type="submit" class="btn btn-primary"><?= $s->trans('submit'); ?></button>
</form>
