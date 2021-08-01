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
   <label for="id" class="form-label" style="background:lightblue"><?= $s->trans('id'); ?></label>
   <?= Html::encode($body['id'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="task_name" class="form-label" style="background:lightblue"><?= $s->trans('task_name'); ?></label>
   <?= Html::encode($body['task_name'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="task_description" class="form-label" style="background:lightblue"><?= $s->trans('task_description'); ?></label>
   <?= Html::encode($body['task_description'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="task_price" class="form-label" style="background:lightblue"><?= $s->trans('task_price'); ?></label>
   <?= Html::encode($body['task_price'] ?? ''); ?>
 </div>
 <div class="mb-3 form-group has-feedback">
        <label class="form-label" style="background:lightblue" for="task_finish_date"><?= $s->trans('task_finish_date'); ?></label>
        <?php
            $fdate = $body['task_finish_date'] ?? null;
            if ($fdate && $fdate != "0000-00-00") {
                //use the DateHelper
                $datehelper = new DateHelper();
                $fdate = $datehelper->date_from_mysql($fdate, false, $s);
            } else {
                $fdate = null;
            }
        ?>      
        <?= Html::encode($fdate); ?>        
 </div>
 <div class="mb3 form-group">
   <label for="task_status" class="form-label" style="background:lightblue"><?= $s->trans('task_status'); ?></label>
   <?= Html::encode($body['task_status'] ?? ''); ?>
 </div>
</div>
