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
  <label for="start_date" class="form-label" style="background:lightblue"><?= $s->trans('start_date'); ?>  </label>
<?php $date = $body['start_date']; if ($date && $date != "0000-00-00") {    $datehelper = new DateHelper();  $date = $datehelper->date_from_mysql($date, false, $s);} else {  $date = null;}?><?= Html::encode($date); ?></div>
<div class="mb3 form-group">
  <label for="end_date" class="form-label" style="background:lightblue"><?= $s->trans('end_date'); ?>  </label>
<?php $date = $body['end_date']; if ($date && $date != "0000-00-00") {    $datehelper = new DateHelper();  $date = $datehelper->date_from_mysql($date, false, $s);} else {  $date = null;}?><?= Html::encode($date); ?></div>
 <div class="mb3 form-group">
<label for="frequency" class="form-label" style="background:lightblue">Frequency</label>
   <?= Html::encode($body['frequency'] ?? ''); ?>
 </div>
<div class="mb3 form-group">
  <label for="next_date" class="form-label" style="background:lightblue"><?= $s->trans('next_date'); ?>  </label>
<?php $date = $body['next_date']; if ($date && $date != "0000-00-00") {    $datehelper = new DateHelper();  $date = $datehelper->date_from_mysql($date, false, $s);} else {  $date = null;}?><?= Html::encode($date); ?></div>
 <div class="mb3 form-group">
   <label for="inv_id" class="form-label" style="background:lightblue"><?= $s->trans('inv'); ?></label>
   <?= $recurring->getInv()->number;?>
 </div>
</div>
