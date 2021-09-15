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
<form id="QuoteForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('quotes_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="inv_id">Inv</label>
    <select name="inv_id" id="inv_id" class="form-control simple-select">
       <option value="0">Inv</option>
         <?php foreach ($invs as $inv) { ?>
          <option value="<?= $inv->id; ?>"
           <?php $s->check_select(Html::encode($body['inv_id'] ?? ''), $inv->id) ?>
           ><?= $inv->id; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
    <label for="client_id">Client</label>
    <select name="client_id" id="client_id" class="form-control simple-select">
       <option value="0"><?= $s->trans('client'); ?></option>
         <?php foreach ($clients as $client) { ?>
          <option value="<?= $client->id; ?>"
           <?php $s->check_select(Html::encode($body['client_id'] ?? ''), $client->client_name) ?>
           ><?= $client->client_name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
                <label for="group_id"><?= $s->trans('invoice_group'); ?>: </label>
                <select name="group_id" id="group_id"
                	class="form-control simple-select" data-minimum-results-for-search="Infinity">
                    <?php foreach ($groups as $group) { ?>
                        <option value="<?php echo $group->id; ?>"
                            <?= $s->check_select($s->get_setting('default_quote_group'), $group->id); ?>>
                            <?= Html::encode($group->name); ?>
                        </option>
                    <?php } ?>
                </select>
 </div>
 <div class="mb3 form-group">
   <input type="hidden" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
 <div class="mb-3 form-group has-feedback"> <?php  $date = $body['date_created'] ?? null; 
$datehelper = new DateHelper(); 
if ($date && $date !== "0000-00-00") { 
    $date = $datehelper->date_from_mysql($date, false, $s); 
} else { 
    $date = null; 
} 
   ?>  
<label form-label for="date_created"><?= $s->trans('date_created') ." (".  $datehelper->date_format_datepicker($s).") "; ?></label>
<div class="mb3 input-group"> 
<input type="text" name="date_created" id="date_created" placeholder="<?= $datehelper->date_format_datepicker($s); ?>" 
       class="form-control data-datepicker" 
       value="<?php if ($date <> null) {echo Html::encode($date);} ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div>   
<div class="mb-3 form-group has-feedback"> <?php  $date = $body['date_modified'] ?? null; 
$datehelper = new DateHelper(); 
if ($date && $date !== "0000-00-00") { 
    $date = $datehelper->date_from_mysql($date, false, $s); 
} else { 
    $date = null; 
} 
?> 
<div class="mb-3 form-group has-feedback"> <?php  $date = $body['date_expires'] ?? null; 
$datehelper = new DateHelper(); 
if ($date && $date !== "0000-00-00") { 
    $date = $datehelper->date_from_mysql($date, false, $s); 
} else { 
    $date = null; 
} 
?>  
<label form-label for="expires"><?= 'Date Expires ('.  $datehelper->date_format_datepicker($s).") "; ?></label>
<div class="mb3 input-group"> 
<input type="text" name="expires" id="date_expires" placeholder="<?= $datehelper->date_format_datepicker($s); ?>" 
       class="form-control data-datepicker" 
       value="<?php if ($date <> null) {echo Html::encode($date);} ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div>   <div class="mb3 form-group">
   <label for="number">Number</label>
   <input type="text" name="number" id="number" class="form-control"
 value="<?= Html::encode($body['number'] ??  ''); ?>">
 </div>
<div class="form-group">
  <label for="discount_amount"><?= $s->trans('discount'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="discount_amount" id="discount_amount" class="form-control"
              value="<?= $s->format_amount($body['discount_amount'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
<div class="form-group">
  <label for="discount_percent">Discount Percent</label>
      <div class="input-group has-feedback">
          <input type="text" name="discount_percent" id="discount_percent" class="form-control"
              value="<?= $s->format_amount($body['discount_percent'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
 <div class="mb3 form-group">
   <label for="url">Url Key</label>
   <input type="text" name="url_key" id="url_key" class="form-control"
 value="<?= Html::encode($body['url_key'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="password"><?= $s->trans('quote_pre_password'); ?></label>
   <input type="text" name="password" id="password" class="form-control"
 value="<?= Html::encode($body['password'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="notes"><?= $s->trans('notes'); ?></label>
   <input type="text" name="notes" id="notes" class="form-control"
 value="<?= Html::encode($body['notes'] ??  ''); ?>">
 </div>

</div>

</div>

</div>
</form>
