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
<form id="CompanyForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('companies_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
   <input type="hidden" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
 <div  class="form-check form-switch">
      <label for="current" class="form-check-label ">
            <?= $s->trans('active'); ?>
            <input class="form-check-input" id="current" name="current" type="checkbox" value="1"
            <?php $s->check_select(Html::encode($body['current'] ?? ''), 1, '==', true) ?>>
      </label>   
 </div>
 </div>    
 <div class="mb3 form-group">
   <label for="name" required><?= $s->trans('name'); ?></label>
   <input type="text" name="name" id="name" class="form-control"
 value="<?= Html::encode($body['name'] ??  ''); ?>" required>
 </div>
 <div class="mb3 form-group">
   <label for="address_1"><?= $s->trans('street_address'); ?></label>
   <input type="text" name="address_1" id="address_1" class="form-control"
 value="<?= Html::encode($body['address_1'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="address_2"><?= $s->trans('street_address_2'); ?></label>
   <input type="text" name="address_2" id="address_2" class="form-control"
 value="<?= Html::encode($body['address_2'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="city"><?= $s->trans('city'); ?></label>
   <input type="text" name="city" id="city" class="form-control"
 value="<?= Html::encode($body['city'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="state"><?= $s->trans('state'); ?></label>
   <input type="text" name="state" id="state" class="form-control"
 value="<?= Html::encode($body['state'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="zip"><?= $s->trans('zip'); ?></label>
   <input type="text" name="zip" id="zip" class="form-control"
 value="<?= Html::encode($body['zip'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="country"><?= $s->trans('country'); ?></label>
   <input type="text" name="country" id="country" class="form-control"
 value="<?= Html::encode($body['country'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="phone"><?= $s->trans('phone'); ?></label>
   <input type="text" name="phone" id="phone" class="form-control"
 value="<?= Html::encode($body['phone'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="fax"><?= $s->trans('fax'); ?></label>
   <input type="text" name="fax" id="fax" class="form-control"
 value="<?= Html::encode($body['fax'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="email" required><?= $s->trans('email_address'); ?></label>
   <input type="text" name="email" id="email" class="form-control"
 value="<?= Html::encode($body['email'] ??  ''); ?>" required>
 </div>
 <div class="mb3 form-group">
   <label for="web"><?= $s->trans('web'); ?></label>
   <input type="text" name="web" id="web" class="form-control"
 value="<?= Html::encode($body['web'] ??  ''); ?>">
 </div>
</div> 
</div>
</div>
</form>
