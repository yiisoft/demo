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
<form id="InvcustForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('custom_field_form'); ?></h1>
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
