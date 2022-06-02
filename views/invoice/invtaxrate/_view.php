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
<label for="include_item_tax" class="form-label" style="background:lightblue"><?= $s->trans('include_item_tax'); ?></label>
   <?= Html::encode($body['include_item_tax'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="amount" class="form-label" style="background:lightblue"><?= $s->trans('amount'); ?></label>
   <?= Html::encode($body['amount'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="inv_id" class="form-label" style="background:lightblue"><?= $s->trans('inv'); ?></label>
   <?= $invtaxrate->getInv()->getId();?>
 </div>
 <div class="mb3 form-group">
   <label for="tax_rate_id" class="form-label" style="background:lightblue"><?= $s->trans('tax_rate'); ?></label>
   <?= $invtaxrate->getTaxRate()->getId();?>
 </div>
</div>
