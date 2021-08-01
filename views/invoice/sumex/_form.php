<?php

declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;

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
   <label for="id"><?= $s->trans('id'); ?></label>
   <input type="text" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
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
   <label for="diagnosis"><?= $s->trans('diagnosis'); ?></label>
   <input type="text" name="diagnosis" id="diagnosis" class="form-control"
 value="<?= Html::encode($body['diagnosis'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="observations"><?= $s->trans('observations'); ?></label>
   <input type="text" name="observations" id="observations" class="form-control"
 value="<?= Html::encode($body['observations'] ??  ''); ?>">
 </div>
 <div class="mb-3 form-group has-feedback"> <label form-label for="treatmentstart"><?= $s->trans('treatmentstart') .'  YYYY-MM-DD'; ?></label>; <?php  $date = $body['treatmentstart'] ?? null; 
$datehelper = new DateHelper(); 
if ($date && $date !== "0000-00-00") { 
    $date = $datehelper->date_from_mysql($date, false, $s); 
} else { 
    $date = null; 
} 
   ?>  
 <div class="mb3 input-group"> 
<input type="text" name="treatmentstart" id="treatmentstart" placeholder="YYYY-MM-DD" 
       class="form-control data-datepicker" 
       value="<?php if ($date <> null) {echo Html::encode($datehelper->date_to_mysql($date, $s));} ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div>   <div class="mb-3 form-group has-feedback"> <label form-label for="treatmentend"><?= $s->trans('treatmentend') .'  YYYY-MM-DD'; ?></label>; <?php  $date = $body['treatmentend'] ?? null; 
$datehelper = new DateHelper(); 
if ($date && $date !== "0000-00-00") { 
    $date = $datehelper->date_from_mysql($date, false, $s); 
} else { 
    $date = null; 
} 
   ?>  
 <div class="mb3 input-group"> 
<input type="text" name="treatmentend" id="treatmentend" placeholder="YYYY-MM-DD" 
       class="form-control data-datepicker" 
       value="<?php if ($date <> null) {echo Html::encode($datehelper->date_to_mysql($date, $s));} ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div>   <div class="mb-3 form-group has-feedback"> <label form-label for="casedate"><?= $s->trans('casedate') .'  YYYY-MM-DD'; ?></label>; <?php  $date = $body['casedate'] ?? null; 
$datehelper = new DateHelper(); 
if ($date && $date !== "0000-00-00") { 
    $date = $datehelper->date_from_mysql($date, false, $s); 
} else { 
    $date = null; 
} 
   ?>  
 <div class="mb3 input-group"> 
<input type="text" name="casedate" id="casedate" placeholder="YYYY-MM-DD" 
       class="form-control data-datepicker" 
       value="<?php if ($date <> null) {echo Html::encode($datehelper->date_to_mysql($date, $s));} ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div>   <div class="mb3 form-group">
   <label for="casenumber"><?= $s->trans('casenumber'); ?></label>
   <input type="text" name="casenumber" id="casenumber" class="form-control"
 value="<?= Html::encode($body['casenumber'] ??  ''); ?>">
 </div>

</div>
</form>
