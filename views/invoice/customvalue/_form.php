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
<form id="CustomValueForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('customvalues_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="custom_field_id">Custom field</label>
    <select name="custom_field_id" id="custom_field_id" class="form-control">
       <option value="0">Custom field</option>
         <?php foreach ($custom_fields as $custom_field) { ?>
          <option value="<?= $custom_field->getId(); ?>"
           <?php $s->check_select(Html::encode($body['custom_field_id'] ?? ''), $custom_field->getId()) ?>
           ><?= $custom_field->getId(); ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <input type="hidden" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="value"><?= $s->trans('value'); ?></label>
   <input type="text" name="value" id="value" class="form-control"
 value="<?= Html::encode($body['value'] ??  ''); ?>">
 </div>

</div>

</div>

</div>
</form>
