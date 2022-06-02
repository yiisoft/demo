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
<label for="tax_code" class="form-label" style="background:lightblue"><?= $s->trans('tax_code'); ?></label>
   <?= Html::encode($body['tax_code'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="iban" class="form-label" style="background:lightblue"><?= $s->trans('user_iban'); ?></label>
   <?= Html::encode($body['iban'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="gln" class="form-label" style="background:lightblue"><?= $s->trans('gln'); ?></label>
   <?= Html::encode($body['gln'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="rcc" class="form-label" style="background:lightblue"><?= $s->trans('sumex_rcc'); ?></label>
   <?= Html::encode($body['rcc'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="company_id" class="form-label" style="background:lightblue"><?= $s->trans('company')." ". $s->trans('name'); ?></label>
   <?= Html::encode($companyprivate->getCompany()->name);?>
 </div>
</div>
