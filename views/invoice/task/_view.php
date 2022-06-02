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
<label for="name" class="form-label" style="background:lightblue"><?= $s->trans('name'); ?></label>
   <?= Html::encode($body['name'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="description" class="form-label" style="background:lightblue"><?= $s->trans('description'); ?></label>
   <?= Html::encode($body['description'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="price" class="form-label" style="background:lightblue"><?= $s->trans('price'); ?></label>
   <?= Html::encode($body['price'] ?? ''); ?>
 </div>
<div class="mb3 form-group">
  <label for="finish_date" class="form-label" style="background:lightblue"><?= $s->trans('task_finish_date'); ?>  </label>
<?php $date = $body['finish_date']; if ($date && $date != "0000-00-00") {    $datehelper = new DateHelper($s);  $date = $datehelper->date_from_mysql($date);} else {  $date = null;}?><?= Html::encode($date); ?></div>
 <div class="mb3 form-group">
<label for="status" class="form-label" style="background:lightblue"><?= $s->trans('status'); ?></label>
   <?= Html::encode($body['status'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="project_id" class="form-label" style="background:lightblue"><?= $s->trans('project'); ?></label>
   <?= $task->getProject()->name;?>
 </div>
 <div class="mb3 form-group">
   <label for="tax_rate_id" class="form-label" style="background:lightblue"><?= $s->trans('tax_rate'); ?></label>
   <?= $task->getTaxRate()->tax_rate_name;?>
 </div>
</div>
