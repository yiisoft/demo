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

$datehelper = new DateHelper($s); 
?>
<h1><?= Html::encode($title) ?></h1>
<div class="row">
<div class="mb3 form-group">
  <label for="date_created" class="form-label" style="background:lightblue"><?= $s->trans('date_created'); ?>  </label>
<?php $date = $body['date_created']; if ($date && $date != "0000-00-00") { $date = $datehelper->date_from_mysql($date);} else {  $date = null;}?><?= Html::encode($date); ?></div>
 <div class="mb3 form-group">
<label for="date_modified" class="form-label" style="background:lightblue">Date Modified</label>
   <?php $date = $body['date_modified']; if ($date && $date != "0000-00-00") {  $date = $datehelper->date_from_mysql($date);} else {  $date = null;}?><?= Html::encode($date); ?></div>
 </div>
<div class="mb3 form-group">
  <label for="date_expires" class="form-label" style="background:lightblue">Date Expires</label>
<?php $date = $body['date_expires']; if ($date && $date != "0000-00-00") {  $date = $datehelper->date_from_mysql($date);} else {  $date = null;}?><?= Html::encode($date); ?></div>
 <div class="mb3 form-group">
<label for="number" class="form-label" style="background:lightblue">Number</label>
   <?= Html::encode($body['number'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="discount_amount" class="form-label" style="background:lightblue">Discount Amount</label>
   <?= Html::encode($body['discount_amount'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="discount_percent" class="form-label" style="background:lightblue">Discount Percent</label>
   <?= Html::encode($body['discount_percent'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="url_key" class="form-label" style="background:lightblue">Url Key</label>
   <?= Html::encode($body['url_key'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="password" class="form-label" style="background:lightblue"><?= $s->trans('password'); ?></label>
   <?= Html::encode($body['password'] ?? ''); ?>
 </div>
<label for="payment_method" class="form-label" style="background:lightblue"><?= $s->trans('payment_method'); ?></label>
   <?= Html::encode($body['payment_method'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="terms" class="form-label" style="background:lightblue"><?= $s->trans('terms'); ?></label>
   <?= Html::encode($body['terms'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="inv_id" class="form-label" style="background:lightblue">Invoice</label>
  <?= Html::encode($body['inv_id'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="client_id" class="form-label" style="background:lightblue"><?= $s->trans('client'); ?></label>
   <?= $quote->getClient()->client_name; ?>
 </div>
 <div class="mb3 form-group">
   <label for="group_id" class="form-label" style="background:lightblue">Group</label>
   <?= $quote->getGroup()->name; ?>
 </div>
</div>