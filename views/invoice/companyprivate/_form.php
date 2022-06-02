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
<form id="CompanyPrivateForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('companyprivates_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="company_id" required><?= $company_public; ?></label>
    <select name="company_id" id="company_id" class="form-control" required>
       <option value=""><?= $company_public; ?></option>
         <?php foreach ($companies as $company) { ?>
          <option value="<?= $company->getId(); ?>"
           <?php $s->check_select(Html::encode($body['company_id'] ?? ''), $company->getId()) ?>
           ><?= $company->getName(); ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <input type="hidden" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="tax_code"><?= $s->trans('tax_code'); ?></label>
   <input type="text" name="tax_code" id="tax_code" class="form-control"
 value="<?= Html::encode($body['tax_code'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="iban"><?= $s->trans('user_iban'); ?></label>
   <input type="text" name="iban" id="iban" class="form-control"
 value="<?= Html::encode($body['iban'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="gln"><?= $s->trans('gln'); ?></label>
   <input type="text" name="gln" id="gln" class="form-control"
 value="<?= Html::encode($body['gln'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="rcc"><?= $s->trans('sumex_rcc'); ?></label>
   <input type="text" name="rcc" id="rcc" class="form-control"
 value="<?= Html::encode($body['rcc'] ??  ''); ?>">
 </div>

</div>

</div>

</div>
</form>
