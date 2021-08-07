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
<form id="InvForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('invs_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
  <div class="mb3 form-group">
    <label for="group_id">Group</label>
    <select name="group_id" id="group_id" class="form-control simple-select">
       <option value="0">Group</option>
         <?php foreach ($groups as $group) { ?>
          <option value="<?= $group->id; ?>"
           <?php $s->check_select(Html::encode($body['group_id'] ?? ''), $group->id) ?>
           ><?= $group->name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
    <label for="client_id">Client</label>
    <select name="client_id" id="client_id" class="form-control simple-select">
       <option value="0">Client</option>
         <?php foreach ($clients as $client) { ?>
          <option value="<?= $client->id; ?>"
           <?php $s->check_select(Html::encode($body['client_id'] ?? ''), $client->id) ?>
           ><?= $client->client_name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <label for="password"><?= $s->trans('invoice_password'); ?></label>
   <input type="text" name="password" id="password" class="form-control"
 value="<?= Html::encode($body['password'] ??  ''); ?>">
 </div>
 
 <?php  $date = $body['date_created'] ?? null; 
$datehelper = new DateHelper(); 
if ($date && $date !== "0000-00-00") { 
    $date = $datehelper->date_from_mysql($date, false, $s); 
} else { 
    $date = null; 
} 
   ?>
<div class="mb-3 form-group has-feedback"><label form-label for="date_created"><?= $s->trans('invoice_date'); ?></label> 
 <div class="input-group"> 
<input type="text" name="date_created" id="date_created" placeholder="<?= $s->trans('invoice_date').' ('.$datehelper->date_format_datepicker($s).')';?>" 
       class="form-control data-datepicker" 
       value="<?php 
                echo Html::encode($date); 
              ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div> 
 <div class="mb3 form-group">
   <label for="terms"><?= $s->trans('invoice_terms'); ?></label>
   <input type="text" name="terms" id="terms" class="form-control"
 value="<?= Html::encode($body['terms'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="payment_method"><?= $s->trans('payment_method'); ?></label>
   <input type="text" name="payment_method" id="payment_method" class="form-control"
 value="<?= Html::encode($body['payment_method'] ??  ''); ?>">
 </div>
 
</div>

</div>
</div>

</form>