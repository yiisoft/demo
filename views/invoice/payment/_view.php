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
  <label for="date" class="form-label" style="background:lightblue"><?= $s->trans('date'); ?>  </label>
<?php $date = $body['date']; if ($date && $date != "0000-00-00") {    $datehelper = new DateHelper($s);  $date = $datehelper->date_from_mysql($date);} else {  $date = null;}?><?= Html::encode($date); ?></div>
 <div class="mb3 form-group">
<label for="amount" class="form-label" style="background:lightblue"><?= $s->trans('amount'); ?></label>
   <?= Html::encode($body['amount'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="note" class="form-label" style="background:lightblue"><?= $s->trans('note'); ?></label>
   <?= Html::encode($body['note'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="inv_id" class="form-label" style="background:lightblue">Invoice Number</label>
   <?= $payment->getInv()->getId();?>
 </div>
 <div class="mb3 form-group">
   <label for="payment_method_id" class="form-label" style="background:lightblue"><?= $s->trans('payment_method'); ?></label>
   <?= $payment->getPaymentMethod()->getId();?>
 </div>
</div>
