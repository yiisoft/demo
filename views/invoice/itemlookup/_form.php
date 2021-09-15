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
<form id="ItemLookupForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('itemlookups_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
   <input type="hidden" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="name"><?= $s->trans('name'); ?></label>
   <input type="text" name="name" id="name" class="form-control"
 value="<?= Html::encode($body['name'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="description"><?= $s->trans('description'); ?></label>
   <input type="text" name="description" id="description" class="form-control"
 value="<?= Html::encode($body['description'] ??  ''); ?>">
 </div>
<div class="form-group">
  <label for="price"><?= $s->trans('price'); ?></label>
      <div class="input-group has-feedback">
          <input type="text" name="price" id="price" class="form-control"
              value="<?= $s->format_amount($body['price'] ?? ''); ?>">
              <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
      </div>
</div>

</div>

</div>

</div>
</form>
