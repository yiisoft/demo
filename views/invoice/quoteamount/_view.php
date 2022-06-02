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
<label for="item_subtotal" class="form-label" style="background:lightblue">Item Subtotal</label>
   <?= Html::encode($body['item_subtotal'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="item_tax_total" class="form-label" style="background:lightblue">Item Tax Total</label>
   <?= Html::encode($body['item_tax_total'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="tax_total" class="form-label" style="background:lightblue">Tax Total</label>
   <?= Html::encode($body['tax_total'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="total" class="form-label" style="background:lightblue">Total</label>
   <?= Html::encode($body['total'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="quote_id" class="form-label" style="background:lightblue">Quote</label>
   <?= $quoteamount->getQuote()->getId(); ?>
 </div>
</div>
