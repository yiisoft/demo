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
<div class="row">
 <div class="mb3 form-group">
<label for="type" class="form-label" style="background:lightblue"><?= $s->trans('type'); ?></label>
   <?= Html::encode($body['type'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="active" class="form-label" style="background:lightblue"><?= $s->trans('active'); ?></label>
   <?= Html::encode($body['active'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="date_created" class="form-label" style="background:lightblue"><?= $s->trans('date_created'); ?></label>
   <?= Html::encode($body['date_created'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="date_modified" class="form-label" style="background:lightblue"><?= $s->trans('date_modified'); ?></label>
   <?= Html::encode($body['date_modified'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="language" class="form-label" style="background:lightblue"><?= $s->trans('language'); ?></label>
   <?= Html::encode($body['language'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="name" class="form-label" style="background:lightblue"><?= $s->trans('name'); ?></label>
   <?= Html::encode($body['name'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="company" class="form-label" style="background:lightblue"><?= $s->trans('company'); ?></label>
   <?= Html::encode($body['company'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="address_1" class="form-label" style="background:lightblue"><?= $s->trans('address_1'); ?></label>
   <?= Html::encode($body['address_1'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="address_2" class="form-label" style="background:lightblue"><?= $s->trans('address_2'); ?></label>
   <?= Html::encode($body['address_2'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="city" class="form-label" style="background:lightblue"><?= $s->trans('city'); ?></label>
   <?= Html::encode($body['city'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="state" class="form-label" style="background:lightblue"><?= $s->trans('state'); ?></label>
   <?= Html::encode($body['state'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="zip" class="form-label" style="background:lightblue"><?= $s->trans('zip'); ?></label>
   <?= Html::encode($body['zip'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="country" class="form-label" style="background:lightblue"><?= $s->trans('country'); ?></label>
   <?= Html::encode($body['country'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="phone" class="form-label" style="background:lightblue"><?= $s->trans('phone'); ?></label>
   <?= Html::encode($body['phone'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="fax" class="form-label" style="background:lightblue"><?= $s->trans('fax'); ?></label>
   <?= Html::encode($body['fax'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="mobile" class="form-label" style="background:lightblue"><?= $s->trans('mobile'); ?></label>
   <?= Html::encode($body['mobile'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="email" class="form-label" style="background:lightblue"><?= $s->trans('email'); ?></label>
   <?= Html::encode($body['email'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="password" class="form-label" style="background:lightblue"><?= $s->trans('password'); ?></label>
   <?= Html::encode($body['password'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="web" class="form-label" style="background:lightblue"><?= $s->trans('web'); ?></label>
   <?= Html::encode($body['web'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="tax_code" class="form-label" style="background:lightblue"><?= $s->trans('tax_code'); ?></label>
   <?= Html::encode($body['tax_code'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="all_clients" class="form-label" style="background:lightblue"><?= $s->trans('all_clients'); ?></label>
   <?= Html::encode($body['all_clients'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="salt" class="form-label" style="background:lightblue"><?= $s->trans('salt'); ?></label>
   <?= Html::encode($body['salt'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="passwordreset_token" class="form-label" style="background:lightblue"><?= $s->trans('passwordreset_token'); ?></label>
   <?= Html::encode($body['passwordreset_token'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="subscribernumber" class="form-label" style="background:lightblue"><?= $s->trans('subscribernumber'); ?></label>
   <?= Html::encode($body['subscribernumber'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="iban" class="form-label" style="background:lightblue"><?= $s->trans('iban'); ?></label>
   <?= Html::encode($body['iban'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="gln" class="form-label" style="background:lightblue"><?= $s->trans('gln'); ?></label>
   <?= Html::encode($body['gln'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="rcc" class="form-label" style="background:lightblue"><?= $s->trans('rcc'); ?></label>
   <?= Html::encode($body['rcc'] ?? ''); ?>
 </div>
</div>
