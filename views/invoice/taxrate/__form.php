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
<form id="taxrateForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
        <h1 class="headerbar-title"><?= $title; ?></h1>
        <?php
            $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]);
            echo (string)$response->getBody();
        ?>
        <div class="mb-3 form-group btn-group-sm">
        </div>
</div>
  <div class="row">
    <div class="mb-3 form-group">
        <input type="text" class="form-control" name="tax_rate_name" id="tax_rate_name" placeholder="Tax Rate Name" value="<?= Html::encode($body['tax_rate_name'] ?? ''); ?>" required>
    </div>
    <div class="mb-3 form-group">
        <input type="text" class="form-control" name="tax_rate_percent" id="tax_rate_percent" placeholder="Tax Rate Percent" value="<?= Html::encode($body['tax_rate_percent'] ?? ''); ?>" required>
        <span class="form-control-feedback">%</span>
    </div>
    <div  class="p-2">
        <label for="tax_rate_default" class="control-label ">
            <?= $translator->translate('invoice.default'); ?>
            <input id="tax_rate_default" name="tax_rate_default" type="checkbox" value="1"
            <?php $s->check_select(Html::encode($body['tax_rate_default'] ?? ''), 1, '==', true) ?>>
        </label>   
    </div>        
  </div>      
</form>
 