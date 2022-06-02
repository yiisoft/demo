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
<div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="client_id">Client</label>
    <select name="client_id" id="client_id" class="form-control simple-select">
       <option value=""><?= $s->trans('client'); ?></option>
         <?php foreach ($clients as $client) { ?>
          <option value="<?= $client->getId(); ?>"
           <?php $s->check_select(Html::encode($body['client_id'] ?? ''), $client->getId()) ?>
           ><?= $client->client_name; ?></option>
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
<label form-label for="date"><?= $s->trans('date') ." (".  $datehelper->display().") "; ?></label><div class="mb3 input-group"> 
<input type="text" name="date" id="date" placeholder="<?= $datehelper->display(); ?>" 
       class="form-control data-datepicker" 
       value="<?php if ($date <> null) {echo Html::encode($date);} ?>"> 
<span class="input-group-text"> 
<i class="fa fa-calendar fa-fw"></i> 
 </span> 
</div>
</div>   <div class="mb3 form-group">
   <label for="note"><?= $s->trans('note'); ?></label>
   <input type="text" name="note" id="note" class="form-control"
 value="<?= Html::encode($body['note'] ??  ''); ?>">
 </div>

</div>

</div>
