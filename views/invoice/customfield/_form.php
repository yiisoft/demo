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
<form id="CustomFieldForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('customfields_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
   <label for="table"><?= $s->trans('table'); ?></label>
   <input type="text" name="table" id="table" class="form-control"
 value="<?= Html::encode($body['table'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="label"><?= $s->trans('label'); ?></label>
   <input type="text" name="label" id="label" class="form-control"
 value="<?= Html::encode($body['label'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="type"><?= $s->trans('type'); ?></label>
   <input type="text" name="type" id="type" class="form-control"
 value="<?= Html::encode($body['type'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="location">Location</label>
   <input type="text" name="location" id="location" class="form-control"
 value="<?= Html::encode($body['location'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="order"><?= $s->trans('order'); ?></label>
   <input type="text" name="order" id="order" class="form-control"
 value="<?= Html::encode($body['order'] ??  ''); ?>">
 </div>

</div>

</div>

</div>
</form>
