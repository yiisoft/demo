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
<form id="PaymentForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('payments_form'); ?></h1>
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
    <label for="payment_method_id">Payment method</label>
    <select name="payment_method_id" id="payment_method_id" class="form-control simple-select">
       <option value="0">Payment method</option>
         <?php foreach ($payment_methods as $payment_method) { ?>
          <option value="<?= $payment_method->id; ?>"
           <?php $s->check_select(Html::encode($body['payment_method_id'] ?? ''), $payment_method->id) ?>
           ><?= $payment_method->id; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <input type="hidden" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
 <div class="mb-3 form-group has-feedback"> <?php  $date = $body['date'] ?? null; 
$datehelper = new DateHelper($s); 
if ($date && $date !== "0000-00-00") { 
    $date = $datehelper->date_from_mysql($date); 
} else { 
    $date = null; 
} 
   ?>  
<label form-label for="date"><?= $s->trans('date') ." (".  $datehelper->date_format_datepicker($s).") "; ?></label><div class="mb3 input-group"> 
<input type="text" name="date" id="date" placeholder="<?= $datehelper->date_format_datepicker($s); ?>" 
       class="form-control data-datepicker" 
       value="<?php if ($date <> null) {echo Html::encode($date);} ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div>  <div class="form-group">
  <label for="amount"><?= $s->trans('amount'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="amount" id="amount" class="form-control"
              value="<?= $s->format_amount($body['amount'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>
 <div class="mb3 form-group">
   <label for="note"><?= $s->trans('note'); ?></label>
   <input type="text" name="note" id="note" class="form-control"
 value="<?= Html::encode($body['note'] ??  ''); ?>">
 </div>

</div>

</div>

</div>
</form>
