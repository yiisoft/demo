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
    <label for="project_id" required>Project</label>
    <select name="project_id" id="project_id" class="form-control" required>
       <option value="">Project</option>
         <?php foreach ($projects as $project) { ?>
          <option value="<?= $project->getId(); ?>"
           <?php $s->check_select(Html::encode($body['project_id'] ?? ''), $project->getId()) ?>
           ><?= $project->getName(); ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
    <label for="tax_rate_id" required><?= $s->trans('tax_rate'); ?></label>
    <select name="tax_rate_id" id="tax_rate_id" class="form-control" required>
       <option value="0"><?= $s->trans('tax_rate'); ?></option>
         <?php foreach ($tax_rates as $tax_rate) { ?>
          <option value="<?= $tax_rate->getTax_rate_id(); ?>"
           <?php $s->check_select(Html::encode($body['tax_rate_id'] ?? ''), $tax_rate->getTax_rate_id()) ?>><?= $tax_rate->getTax_rate_name(); ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <input type="hidden" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="name" required><?= $s->trans('name'); ?></label>
   <input type="text" name="name" id="name" class="form-control"
 value="<?= Html::encode($body['name'] ??  ''); ?>" required>
 </div>
 <div class="mb3 form-group">
   <label for="description" required><?= $s->trans('description'); ?></label>
   <input type="text" name="description" id="description" class="form-control"
 value="<?= Html::encode($body['description'] ??  ''); ?>" required>
 </div>
<div class="form-group">
  <label for="price" required><?= $s->trans('price'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="price" id="price" class="form-control"
              value="<?= $s->format_amount($body['price'] ?? ''); ?>" required>
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
 <div class="mb-3 form-group has-feedback"> 
 <?php  $date = $body['finish_date'] ?? null; 
        $datehelper = new DateHelper($s); 
        if ($date && $date !== "0000-00-00") { 
            $date = $datehelper->date_from_mysql($date); 
        } else { 
            $date = null; 
        } 
?>  
<label form-label for="finish_date" required><?= $s->trans('task_finish_date') ." (".  $datehelper->display().") "; ?></label><div class="mb3 input-group"> 
<input type="text" name="finish_date" id="finish_date" placeholder="<?= $datehelper->display(); ?>" 
       class="form-control input-sm datepicker" 
       value="<?php if ($date <> null) {echo Html::encode($date);} ?>" required role="presentation" autocomplete="off"> 
       <span class="input-group-text"> 
          <i class="fa fa-calendar fa-fw"></i> 
       </span> 
</div>
</div>   <div class="mb3 form-group">
   <label for="status" class="form-label"><?= $s->trans('status'); ?></label>
   <input type="hidden" name="status" value="0">
   <input type="checkbox" name="status" id="status" value="1" <?php $s->check_select(Html::encode($body['status'] ??'' ), 1, '==', true) ?>>
 </div>

</div>

</div>

</div>
<?php $js13 = "$(function () {".
        '$("#finish_date.form-control.input-sm.datepicker").datepicker({dateFormat:"'.$datehelper->display().'"});'.
      '});';
    echo Html::script($js13)->type('module');
?>
</form>
