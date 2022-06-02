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
<form id="MerchantForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('merchants_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="inv_id">Inv</label>
    <select name="inv_id" id="inv_id" class="form-control simple-select">
       <option value="0">Inv</option>
         <?php foreach ($invs as $inv) { ?>
          <option value="<?= $inv->getId(); ?>"
           <?php $s->check_select(Html::encode($body['inv_id'] ?? ''), $inv->getId()) ?>
           ><?= $inv->getId(); ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <label for="successful" class="form-label"><?= $s->trans('successful'); ?></label>
   <input type="hidden" name="successful" value="0">
   <input type="checkbox" name="successful" id="successful" value="1" <?php $s->check_select(Html::encode($body['successful'] ??'' ), 1, '==', true) ?>>
 </div>
 <div class="mb-3 form-group has-feedback"> <?php  $date = $body['date'] ?? null; 
$datehelper = new DateHelper($s); 
if ($date && $date !== "0000-00-00") { 
    $date = $datehelper->date_from_mysql($date); 
} else { 
    $date = null; 
} 
   ?>  
<label form-label for="date"><?= $s->trans('date') ." (". $datehelper->display().") "; ?></label><div class="mb3 input-group"> 
<input type="text" name="date" id="date" placeholder="<?= $datehelper->display(); ?>" 
       class="form-control data-datepicker" 
       value="<?php if ($date <> null) {echo Html::encode($date);} ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div>   <div class="mb3 form-group">
   <label for="driver">Driver</label>
   <input type="text" name="driver" id="driver" class="form-control"
 value="<?= Html::encode($body['driver'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="response">Merchant Response</label>
   <input type="text" name="response" id="response" class="form-control"
 value="<?= Html::encode($body['response'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="reference">Reference</label>
   <input type="text" name="reference" id="reference" class="form-control"
 value="<?= Html::encode($body['reference'] ??  ''); ?>">
 </div>

</div>

</div>

</div>
</form>
