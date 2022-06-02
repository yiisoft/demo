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
<form id="SumexForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('sumexs_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
   <label for="invoice"><?= $s->trans('invoice'); ?></label>
   <input type="text" name="invoice" id="invoice" class="form-control"
 value="<?= Html::encode($body['invoice'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="reason"><?= $s->trans('reason'); ?></label>
   <input type="text" name="reason" id="reason" class="form-control"
 value="<?= Html::encode($body['reason'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="diagnosis"><?= $s->trans('invoice_sumex_diagnosis'); ?></label>
   <input type="text" name="diagnosis" id="diagnosis" class="form-control"
 value="<?= Html::encode($body['diagnosis'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="observations"><?= $s->trans('sumex_observations'); ?></label>
   <input type="text" name="observations" id="observations" class="form-control"
 value="<?= Html::encode($body['observations'] ??  ''); ?>">
 </div>
 <div class="mb-3 form-group has-feedback"><?php  $tdate = $body['treatmentstart'] ?? null; 
$datehelper = new DateHelper($s); 
if ($tdate && $tdate !== "0000-00-00") { 
    $tdate = $datehelper->date_from_mysql($tdate); 
} else { 
    $tdate = null; } 
?>
<label form-label for="treatmentstart"><?= $s->trans('treatment_start') ." (". $datehelper->display().") "; ?></label>
<div class="mb3 input-group">
<input type="text" name="treatmentstart" id="treatmentstart" placeholder="<?= $datehelper->display(); ?>" 
       class="form-control data-datepicker" 
       value="<?php if ($tdate <> null) {echo Html::encode($tdate);} ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div>   <div class="mb-3 form-group has-feedback"><?php  $edate = $body['treatmentend'] ?? null; 
$datehelper = new DateHelper($s); 
if ($edate && $edate !== "0000-00-00") { 
    $edate = $datehelper->date_from_mysql($edate); 
} else { 
    $edate = null; 
} 
   ?>

<label form-label for="treatmentend"><?= $s->trans('treatment_end')." (". $datehelper->display().") "; ?></label> 
<div class="mb3 input-group">
<input type="text" name="treatmentend" id="treatmentend" placeholder="<?= $datehelper->display(); ?>" 
       class="form-control data-datepicker" 
       value="<?php if ($edate <> null) {echo Html::encode($edate);} ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div>   <div class="mb-3 form-group has-feedback"><?php  $cdate = $body['casedate'] ?? null; 
$datehelper = new DateHelper($s); 
if ($cdate && $cdate !== "0000-00-00") { 
    $cdate = $datehelper->date_from_mysql($cdate); 
} else { 
    $cdate = null; 
} 
   ?>

<label form-label for="casedate"><?= $s->trans('case_date')." (". $datehelper->display().") "; ?></label> 
<div class="mb3 input-group">
<input type="text" name="casedate" id="casedate" placeholder="<?= $datehelper->display(); ?>" 
       class="form-control data-datepicker" 
       value="<?php if ($cdate <> null) {echo Html::encode($cdate);} ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div>   <div class="mb3 form-group">
   <label for="casenumber"><?= $s->trans('case_number'); ?></label>
   <input type="text" name="casenumber" id="casenumber" class="form-control"
 value="<?= Html::encode($body['casenumber'] ??  ''); ?>">
 </div>
</div>
</div>
</div>
</form>
