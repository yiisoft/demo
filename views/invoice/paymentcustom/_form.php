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
<form id="PaymentCustomForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('paymentcustoms_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="payment_id">Payment</label>
    <select name="payment_id" id="payment_id" class="form-control simple-select">
       <option value="0">Payment</option>
         <?php foreach ($payments as $payment) { ?>
          <option value="<?= $payment->id; ?>"
           <?php $s->check_select(Html::encode($body['payment_id'] ?? ''), $payment->id) ?>
           ><?= $payment->id; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <input type="hidden" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="fieldid">Field Id</label>
   <input type="text" name="fieldid" id="fieldid" class="form-control"
 value="<?= Html::encode($body['fieldid'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="fieldvalue">Field Value</label>
   <input type="text" name="fieldvalue" id="fieldvalue" class="form-control"
 value="<?= Html::encode($body['fieldvalue'] ??  ''); ?>">
 </div>

</div>

</div>

</div>
</form>
