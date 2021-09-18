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
   <label for="invoice" class="form-label" style="background:lightblue"><?= $s->trans('invoice'); ?></label>
   <?= Html::encode($body['invoice'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="reason" class="form-label" style="background:lightblue"><?= $s->trans('reason'); ?></label>
   <?= Html::encode($body['reason'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="diagnosis" class="form-label" style="background:lightblue"><?= $s->trans('invoice_sumex_diagnosis'); ?></label>
   <?= Html::encode($body['diagnosis'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="observations" class="form-label" style="background:lightblue"><?= $s->trans('sumex_observations'); ?></label>
   <?= Html::encode($body['observations'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="treatmentstart" class="form-label" style="background:lightblue"><?= $s->trans('treatment_start'); ?></label>
   <?php
        $tdate = $body['treatmentstart'];
        if ($tdate && $tdate != "0000-00-00") {
                //use the DateHelper
            $datehelper = new DateHelper($s);
            $tdate = $datehelper->date_from_mysql($tdate);
        } else {
            $tdate = null;
        }
    ?>      
    <?= Html::encode($tdate); ?>
 </div>
 <div class="mb3 form-group">
   <label for="treatmentend" class="form-label" style="background:lightblue"><?= $s->trans('treatment_end'); ?></label>
   <?php
        $edate = $body['treatmentend'];
        if ($edate && $edate != "0000-00-00") {
                //use the DateHelper
            $datehelper = new DateHelper($s);
            $edate = $datehelper->date_from_mysql($edate);
        } else {
            $edate = null;
        }
    ?>      
    <?= Html::encode($edate); ?>
 </div>
 <div class="mb3 form-group">
   <label for="casedate" class="form-label" style="background:lightblue"><?= $s->trans('case_date'); ?></label>
   <?php
        $cdate = $body['casedate'];
        if ($cdate && $cdate != "0000-00-00") {
                //use the DateHelper
            $datehelper = new DateHelper($s);
            $cdate = $datehelper->date_from_mysql($cdate);
        } else {
            $cdate = null;
        }
    ?>      
    <?= Html::encode($cdate); ?>
 </div>
 <div class="mb3 form-group">
   <label for="casenumber" class="form-label" style="background:lightblue"><?= $s->trans('case_number'); ?></label>
   <?= Html::encode($body['casenumber'] ?? ''); ?>
 </div>
</div>
