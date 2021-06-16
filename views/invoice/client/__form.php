<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use Yiisoft\Arrays\ArrayHelper;
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

<form id="clientForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data" >
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div class="card">
  <div class="card-header d-flex justify-content-between">
      <?= $s->trans('personal_information'); ?>
      <div  class="p-2">
      <label for="client_active" class="control-label ">
                                <?= $s->trans('active_client'); ?>
                                <input id="client_active" name="client_active" type="checkbox" value="1"
                                    <?php if (Html::encode($body['client_active'] ?? "1")
                                        || !is_numeric($body['client_active'])
                                    ) {
                                        echo 'checked="checked"';
                                    } ?>>
      </label>
      </div>    
  </div>       
  <div class="row">
    <div class="mb-3 form-group">
        <label for="client_name" class="form-label"><?= $s->trans('client_name'); ?><span style="color:red">*</span></label>
        <input type="text" class="form-control" name="client_name" id="client_name" placeholder="<?= $s->trans('client_name'); ?>" value="<?= Html::encode($body['client_name'] ?? '') ?>" required>
    </div>
    <div class="mb-3 form-group">
        <label for="client_surname" class="form-label"><?= $s->trans('client_surname'); ?></label>
        <input type="text" class="form-control" name="client_surname" id="client_surname" placeholder="<?= $s->trans('client_surname'); ?>" value="<?= Html::encode($body['client_surname'] ?? '') ?>">
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="client_language" class="form-label">
            <?php echo $s->trans('language'); ?>
        </label>
        <select name="client_language" id="client_language" class="form-control">
                <option value="system">
                    <?= Html::encode($body['client_language'] ??  $s->trans('language')); ?>
                </option>
                    <?php foreach (ArrayHelper::map($s->expandDirectoriesMatrix($aliases->get('@language'), $level = 0),'name','name') as $language) {
                       Html::encode($body['client_language'] ?? ''); 
                    ?>
                        <option value="<?php echo $language; ?>"
                            <?php $s->check_select($s->get_setting('client_language'), $language) ?>>
                            <?php echo ucfirst($language); ?>
                        </option>
                    <?php } ?>
        </select> 
    </div>  
  </div>
 
</div>
<br>
<div class="card">
  <div class="card-header">
      <?= $s->trans('address'); ?>
  </div>      
  <div class="row">
    <div class="mb-3 form-group">
        <label for="client_address_1" class="form-label"><?= $s->trans('street_address'); ?></label>
        <input type="text" class="form-control" name="client_address_1" id="client_address_1" placeholder="<?= $s->trans('street_address'); ?>" value="<?= Html::encode($body['client_address_1'] ?? '') ?>">
    </div>
    
    <div class="mb-3 form-group">
        <label for="client_address_2" class="form-label"><?= $s->trans('street_address_2'); ?></label>
        <input type="text" class="form-control" name="client_address_2" id="client_address_2" placeholder="<?= $s->trans('street_address_2'); ?>" value="<?= Html::encode($body['client_address_2'] ?? '') ?>">
    </div>
    
    <div class="mb-3 form-group">
        <label for="client_city" class="form-label"><?= $s->trans('city'); ?></label>
        <input type="text" class="form-control" name="client_city" id="client_city" placeholder="<?= $s->trans('city'); ?>" value="<?= Html::encode($body['client_city'] ?? '') ?>">
    </div>
    
    <div class="mb-3 form-group">
        <label for="client_state" class="form-label"><?= $s->trans('state'); ?></label>
        <input type="text" class="form-control" name="client_state" id="client_state" placeholder="<?= $s->trans('state'); ?>" value="<?= Html::encode($body['client_state'] ?? '') ?>">
    </div>
    
    <div class="mb-3 form-group">
        <label for="client_zip" class="form-label"><?= $s->trans('zip'); ?></label>
        <input type="text" class="form-control" name="client_zip" id="client_zip" placeholder="<?= $s->trans('zip'); ?>" value="<?= Html::encode($body['client_zip'] ?? '') ?>">
    </div>
    
    <div class="mb-3 form-group">
        <label for="client_country" class="form-label"><?= $s->trans('country'); ?></label>
            <div class="controls">
                <select name="client_country" id="client_country" class="form-control">
                    <option value=""><?= $s->trans('country'); ?></option>
                    <?php foreach ($countries as $cldr => $country) { ?>
                        <option value="<?php echo $cldr; ?>"
                            <?php $s->check_select($selected_country, $cldr); ?>
                        ><?php echo $country ?></option>
                    <?php } ?>          
                </select>
            </div>
    </div>
  </div>
</div>
<br>
<div class="card">
  <div class="card-header">
      <?= $s->trans('contact_information'); ?>
  </div>      
  <div class="row">
    <div class="mb-3 form-group">
        <label for="client_phone" class="form-label"><?= $s->trans('phone'); ?></label>
        <input type="text" class="form-control" name="client_phone" id="client_phone" placeholder="<?= $s->trans('phone'); ?>" value="<?= Html::encode($body['client_phone'] ?? '') ?>">
    </div>
            
    <div class="mb-3 form-group">
        <label for="client_fax" class="form-label"><?= $s->trans('fax'); ?></label>
        <input type="text" class="form-control" name="client_fax" id="client_fax" placeholder="<?= $s->trans('fax'); ?>" value="<?= Html::encode($body['client_fax'] ?? '') ?>">
    </div>
      
    <div class="mb-3 form-group">
        <label for="client_mobile" class="form-label"><?= $s->trans('mobile'); ?></label>
        <input type="text" class="form-control" name="client_mobile" id="client_mobile" placeholder="<?= $s->trans('mobile'); ?>" value="<?= Html::encode($body['client_mobile'] ?? '') ?>">
    </div>
    
    <div class="mb-3 form-group">
        <label for="client_email" class="form-label"><?= $s->trans('email'); ?><span style="color:red">*</span></label>
        <input type="text" class="form-control" name="client_email" id="client_email" placeholder="<?= $s->trans('email'); ?>" value="<?= Html::encode($body['client_email'] ?? '') ?>" required>
    </div>
       
    <div class="mb-3 form-group">
        <label for="client_web" class="form-label"><?= $s->trans('web'); ?></label>
        <input type="text" class="form-control" name="client_web" id="client_web" placeholder="<?= $s->trans('web'); ?>" value="<?= Html::encode($body['client_web'] ?? '') ?>">
    </div>
  </div>
</div>
<br>
<div class="card">
  <div class="card-header">
      <?= $s->trans('tax_information'); ?>
  </div>      
  <div class="row">     
    <div class="mb-3 form-group">
        <label for="client_vat_id" class="form-label"><?= $s->trans('vat_id'); ?></label>
        <input type="text" class="form-control" name="client_vat_id" id="client_vat_id" placeholder="<?= $s->trans('vat_id'); ?>" value="<?= Html::encode($body['client_vat_id'] ?? '') ?>">
    </div>
    
    <div class="mb-3 form-group">
        <label for="client_tax_code" class="form-label"><?= $s->trans('tax_code'); ?></label>
        <input type="text" class="form-control" name="client_tax_code" id="client_tax_code" placeholder="<?= $s->trans('tax_code'); ?>" value="<?= Html::encode($body['client_tax_code'] ?? '') ?>">
    </div>
  </div>
</div>
<br>
<div class="card">
  <div class="card-header">
      <?= $s->trans('personal_information'); ?>
  </div>      
  <div class="row">
    <div class="mb-3 form-group">
        <label for="client_gender"  class="form-label"><?= $s->trans('gender'); ?></label>
        <div class="controls">
            <select name="client_gender" id="client_gender"
                    class="form-control" data-minimum-results-for-search="Infinity">
                <?php                
                $genders = [
                    $s->trans('gender_male'),
                    $s->trans('gender_female'),
                    $s->trans('gender_other'),
                ];
                foreach ($genders as $key => $val) { ?>
                    <option value=" <?php echo $key; ?>" <?php $s->check_select($key, Html::encode($body['client_gender'] ?? 0 )) ?>>
                        <?php echo $val; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="mb-3 form-group has-feedback">
        <label form-label for="client_birthdate"><?= $s->trans('birthdate') .'  YYYY-MM-DD'; ?></label>
        <?php
            $bdate = $body['client_birthdate'] ?? null;
            $datehelper = new DateHelper();
            if ($bdate && $bdate !== "0000-00-00") {
                //use the DateHelper
                $bdate = $datehelper->date_from_mysql($bdate, false, $s);
            } else {
                $bdate = null;
            }
        ?>        
        <div class="input-group">
            <input type="text" name="client_birthdate" id="client_birthdate" placeholder="YYYY-MM-DD"
                   class="form-control data-datepicker"
                   value="<?php if ($bdate <> null) {echo Html::encode($datehelper->date_to_mysql($bdate, $s));} ?>">
            <span class="input-group-addon">
            <i class="fa fa-calendar fa-fw"></i>
        </span>
        </div>        
    </div>  
    <div class="mb-3 form-group">
        <label for="client_avs" class="form-label"><?= $s->trans('sumex_ssn'); ?></label>
        <input type="text" class="form-control" name="client_avs" id="client_avs" placeholder="<?= $s->trans('sumex_ssn'); ?>" value="<?= Html::encode($body['client_avs'] ?? '') ?>">
    </div>
    
    <div class="mb-3 form-group">
        <label for="client_insurednumber" class="form-label"><?= $s->trans('sumex_insurednumber'); ?></label>
        <input type="text" class="form-control" name="client_insurednumber" id="client_insurednumber" placeholder="<?= $s->trans('sumex_insurednumber'); ?>" value="<?= Html::encode($body['client_insurednumber'] ?? '') ?>">
    </div>
    
    <div class="mb-3 form-group">
        <label for="client_veka" class="form-label"><?= $s->trans('sumex_veka'); ?></label>
        <input type="text" class="form-control" name="client_veka" id="client_veka" placeholder="<?= $s->trans('sumex_veka'); ?>" value="<?= Html::encode($body['client_veka'] ?? '') ?>">
    </div>
    
  </div>
</div>
    
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
