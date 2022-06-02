<?php
declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use Yiisoft\Arrays\ArrayHelper;

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
<form id="UserInvForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('userinvs_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
 <input type="hidden" name="id" id="id" class="form-control"
 value="<?= Html::encode($body['id'] ??  ''); ?>">
 </div>
 
<div class="mb-3 form-group no-margin">
    <label for="users" class="form-label">
        <?php echo $s->trans('users'); ?>
    </label>
    <select name="user_id" id="user_id" class="form-control" required>
                <option value="0"><?= $s->trans('user'); ?></option> 
                <?php foreach ($users as $user) { ?>
                <option value="<?php echo $user->getId(); ?>"
                    <?php $s->check_select(Html::encode($body['user_id'] ?? ''), $user->getId()) ?>>
                    <?php echo ucfirst($user->getLogin()); ?>
                </option>
            <?php } ?>
    </select>
</div>
 
 <div class="mb3 form-group">
   <?php 
      $types = [
          0 => $s->trans('administrator'),
          1 => $s->trans('guest_read_only'),
      ]  
   ?>
   <label for="type"><?= $s->trans('type'); ?></label>
   <select name="type" id="type" class="form-control" required>
            <option><?php Html::encode($body['type'] ?? ''); ?></option>
            <?php foreach ($types as $key => $value) { ?>
                <option value="<?= $key; ?>"
                    <?php $s->check_select(Html::encode($body['type'] ?? ''), $key) ?>>
                    <?php echo $value; ?>
                </option>
            <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <label for="active"><?= $s->trans('active'); ?></label>
   <input id="active" name="active" type="checkbox" value="1"
   <?php $s->check_select(Html::encode($body['active'] ?? ''), 1, '==', true) ?>>
</div>
<div class="mb3 form-group">
   <label for="all_clients"><?= $s->trans('user_all_clients'); ?></label>
   <input id="all_clients" name="all_clients" type="checkbox" value="1"
   <?php $s->check_select(Html::encode($body['all_clients'] ?? ''), 1, '==', true) ?>>
</div>
<div class="mb-3 form-group no-margin">
    <label for="language" class="form-label">
        <?php echo $s->trans('language'); ?>
    </label>
    <select name="language" id="language" class="form-control" required>
            <option><?php Html::encode($body['language'] ?? ''); ?></option>
            <?php foreach (ArrayHelper::map($s->expandDirectoriesMatrix($aliases->get('@language'), $level = 0),'name','name') as $language) { ?>
                <option value="<?php echo $language; ?>"
                    <?php $s->check_select(Html::encode($body['language'] ?? ''), $language) ?>>
                    <?php echo ucfirst($language); ?>
                </option>
            <?php } ?>
    </select>
</div>   
 <div class="mb3 form-group">
   <label for="name"><?= $s->trans('name'); ?></label>
   <input type="text" name="name" id="name" class="form-control" required value="<?= Html::encode($body['name'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="company"><?= $s->trans('company'); ?></label>
   <input type="text" name="company" id="company" class="form-control"
 value="<?= Html::encode($body['company'] ??  ''); ?>">
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
   <label for="mobile"><?= $s->trans('mobile'); ?></label>
   <input type="text" name="mobile" id="mobile" class="form-control"
 value="<?= Html::encode($body['mobile'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="email"><?= $s->trans('email'); ?></label>
   <input type="text" name="email" id="email" class="form-control"
 value="<?= Html::encode($body['email'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="password"><?= $s->trans('password'); ?></label>
   <input type="text" name="password" id="password" class="form-control"
 value="<?= Html::encode($body['password'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="web"><?= $s->trans('web_address'); ?></label>
   <input type="text" name="web" id="web" class="form-control"
 value="<?= Html::encode($body['web'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="tax_code"><?= $s->trans('tax_code'); ?></label>
   <input type="text" name="tax_code" id="tax_code" class="form-control"
 value="<?= Html::encode($body['tax_code'] ??  ''); ?>">
 </div>
 <div class="mb3 form-group">
   <label for="subscribernumber"><?= $s->trans('user_subscriber_number'); ?></label>
   <input type="text" name="subscribernumber" id="subscribernumber" class="form-control"
 value="<?= Html::encode($body['subscribernumber'] ??  ''); ?>">
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
