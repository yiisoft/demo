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
<label for="include_item_tax" class="form-label" style="background:lightblue">Include Item Tax</label>
   <?= Html::encode($body['include_item_tax'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="quote_tax_rate_amount" class="form-label" style="background:lightblue">Quote Tax Rate Amount</label>
   <?= Html::encode($body['quote_tax_rate_amount'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="quote_id" class="form-label" style="background:lightblue">Quote</label>
   <?= $quotetaxrate->getQuote()->getId();?>
 </div>
 <div class="mb3 form-group">
   <label for="tax_rate_id" class="form-label" style="background:lightblue">Tax Rate</label>
   <?= $quotetaxrate->getTaxRate()->tax_rate_name;?>
 </div>
</div>
