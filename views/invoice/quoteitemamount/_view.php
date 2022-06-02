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
<label for="subtotal" class="form-label" style="background:lightblue"><?= $s->trans('subtotal'); ?></label>
   <?= Html::encode($body['subtotal'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="tax_total" class="form-label" style="background:lightblue"><?= $s->trans('tax_total'); ?></label>
   <?= Html::encode($body['tax_total'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="discount" class="form-label" style="background:lightblue"><?= $s->trans('discount'); ?></label>
   <?= Html::encode($body['discount'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="total" class="form-label" style="background:lightblue"><?= $s->trans('total'); ?></label>
   <?= Html::encode($body['total'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="quote_item_id" class="form-label" style="background:lightblue"><?= $s->trans('quote_item'); ?></label>
   <?= $quoteitemamount->getQuoteItem()->getId();?>
 </div>
</div>
