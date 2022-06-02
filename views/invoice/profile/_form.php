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
<form id="ProfileForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title">Profile</h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row"> 
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
    <label for="company_id">Company public</label>
    <select name="company_id" id="company_id" class="form-control">
       <option value="0">Company public</option>
         <?php foreach ($companies as $company) { ?>
          <option value="<?= $company->getId(); ?>"
           <?php $s->check_select(Html::encode($body['company_id'] ?? ''), $company->getId()) ?>
           ><?= $company->name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <label for="mobile"><?= $s->trans('mobile'); ?></label>
   <input type="text" name="mobile" id="mobile" class="form-control"
 value="<?= Html::encode($body['mobile'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="email"><?= $s->trans('email'); ?></label>
   <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter email"
 value="<?= Html::encode($body['email'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="description"><?= $s->trans('description'); ?></label>
   <input type="text" class="form-control" id="description" name="description" aria-describedby="" placeholder="Description"
 value="<?= Html::encode($body['email'] ??  ''); ?>">
 </div>
 </div>

</div>
</div>
</form>
