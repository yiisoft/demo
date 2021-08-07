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
<form id="TaskForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('tasks_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="project_id">Project</label>
    <select name="project_id" id="project_id" class="form-control simple-select">
       <option value="0">Project</option>
         <?php foreach ($projects as $project) { ?>
          <option value="<?= $project->id; ?>"
           <?php $s->check_select(Html::encode($body['project_id'] ?? ''), $project->id) ?>
           ><?= $project->project_name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
    <label for="tax_rate_id">Tax rate</label>
    <select name="tax_rate_id" id="tax_rate_id" class="form-control simple-select">
       <option value="0">Tax rate</option>
         <?php foreach ($tax_rates as $tax_rate) { ?>
          <option value="<?= $tax_rate->id; ?>"
           <?php $s->check_select(Html::encode($body['tax_rate_id'] ?? ''), $tax_rate->id) ?>
           ><?= $tax_rate->tax_rate_name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <label for="task_name"><?= $s->trans('task_name'); ?></label>
   <input type="text" name="task_name" id="task_name" class="form-control"
 value="<?= Html::encode($body['task_name'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="task_description"><?= $s->trans('task_description'); ?></label>
   <input type="text" name="task_description" id="task_description" class="form-control"
 value="<?= Html::encode($body['task_description'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="task_price"><?= $s->trans('task_price'); ?></label>
   <input type="text" name="task_price" id="task_price" class="form-control"
 value="<?= Html::encode($body['task_price'] ??  ''); ?>">
 </div>
    
 <div class="mb-3 form-group has-feedback">
        <?php
            $fdate = $body['task_finish_date'] ?? null;
            $datehelper = new DateHelper();
            if ($fdate && $fdate !== "0000-00-00") {
                //use the DateHelper
                $fdate = $datehelper->date_from_mysql($fdate, false, $s);
            } else {
                $fdate = null;
            }
        ?>
        <label for="task_finish_date"><?= $s->trans('task_finish_date') .' ('.$datehelper->date_format_datepicker($s).')'; ?></label>
        <div class="input-group">
            <input type="text" name="task_finish_date" id="task_finish_date" placeholder="<?= ' ('.$datehelper->date_format_datepicker($s).')';?>YYYY-MM-DD"
                   class="form-control data-datepicker"
                   value="<?php if ($fdate <> null) {echo Html::encode($fdate);} ?>">
            <span class="input-group-text">
            <i class="fa fa-calendar fa-fw"></i>
        </span>
        </div>        
 </div>     
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
</div>
</form>
