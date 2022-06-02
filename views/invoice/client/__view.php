<?php
declare(strict_types=1);

use Yiisoft\Html\Html;
use App\Invoice\Helpers\DateHelper;

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var string $title
 */
$datehelper = new DateHelper($s);
?>

<h1><?= Html::encode($title) ?></h1>

  <div class="row">
    <div class="mb-3 form-group">
        <label for="client_active" class="control-label" style="background:lightblue"><?= $s->trans('active_client'); ?> </label>
                                
        <?php if (($client->getClient_active() == "1") || !is_numeric($client->getClient_active())) {
            echo $s->trans('yes');
        } else {echo $s->trans('no');} ?>
          
    </div>      
    <div class="mb-3 form-group">
        <label for="client_name" class="form-label" style="background:lightblue"><?= $s->trans('client_name'); ?></label>
        <?= Html::encode($client->getClient_name()); ?>
    </div>
    <div class="mb-3 form-group">
        <label for="client_surname" class="form-label" style="background:lightblue"><?= $s->trans('client_surname'); ?></label>
        <?= Html::encode($client->getClient_surname()); ?>
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="client_language" class="form-label" style="background:lightblue"><?php echo $s->trans('language'); ?></label>
        <?= Html::encode($client->getClient_language()); ?>         
    </div>  
  </div>
  <div class="row">
    <div class="mb-3 form-group">
        <label for="client_address_1" class="form-label" style="background:lightblue"><?= $s->trans('street_address'); ?></label>
        <?= Html::encode($client->getClient_address_1()); ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="client_address_2" class="form-label" style="background:lightblue"><?= $s->trans('street_address_2'); ?></label>
        <?= Html::encode($client->getClient_address_2()); ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="client_city" class="form-label" style="background:lightblue"><?= $s->trans('city'); ?></label>
        <?= Html::encode($client->getClient_city()); ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="client_state" class="form-label" style="background:lightblue"><?= $s->trans('state'); ?></label>
        <?= Html::encode($client->getClient_state()); ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="client_zip" class="form-label" style="background:lightblue"><?= $s->trans('zip'); ?></label>
        <?= Html::encode($client->getClient_zip()); ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="client_zip" class="form-label" style="background:lightblue"><?= $s->trans('country'); ?></label>
        <?= Html::encode($client->getClient_country()); ?>            
    </div>
  </div>
  <div class="row">
    <div class="mb-3 form-group">
        <label for="client_zip" class="form-label" style="background:lightblue"><?= $s->trans('phone'); ?></label>        
        <?= Html::encode($client->getClient_phone()); ?>
    </div>            
    <div class="mb-3 form-group">
        <label for="client_fax" class="form-label" style="background:lightblue"><?= $s->trans('fax'); ?></label>
        <?= Html::encode($client->getClient_fax()); ?>
    </div>      
    <div class="mb-3 form-group">
        <label for="client_mobile" class="form-label" style="background:lightblue"><?= $s->trans('mobile'); ?></label>
        <?= Html::encode($client->getClient_mobile()); ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="client_email" class="form-label" style="background:lightblue"><?= $s->trans('email'); ?></label>
        <?= Html::encode($client->getClient_email()); ?>
    </div>       
    <div class="mb-3 form-group">
        <label for="client_web" class="form-label" style="background:lightblue"><?= $s->trans('web'); ?></label>
        <?= Html::encode($client->getClient_web()); ?>
    </div>
  </div>    
  <div class="row">     
    <div class="mb-3 form-group">
        <label for="client_vat_id" class="form-label" style="background:lightblue"><?= $s->trans('vat_id'); ?></label>
        <?= Html::encode($client->getClient_vat_id()); ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="client_tax_code" class="form-label" style="background:lightblue"><?= $s->trans('tax_code'); ?></label>
        <?= Html::encode($client->getClient_tax_code()); ?>
    </div>
  </div>
  <div class="row">
    <div class="mb-3 form-group">
        <label for="client_gender"  class="form-label" style="background:lightblue"><?= $s->trans('gender'); ?></label>
        <?php                
                $genders = [
                    $s->trans('gender_male'),
                    $s->trans('gender_female'),
                    $s->trans('gender_other'),
                ];
                foreach ($genders as $key => $val) { 
                        if ($key == $client->getClient_gender()){
                            echo Html::encode($val ?? '');
                        } 
                }    
        ?>
    </div>
    <div class="mb-3 form-group has-feedback">
        <label class="form-label" style="background:lightblue" for="client_birthdate"><?= $s->trans('birthdate'); ?></label>
        <?php
            $bdate = $body['client_birthdate'] ?? null;
            if ($bdate && $bdate != "0000-00-00") {
                //use the DateHelper
                $datehelper = new DateHelper($s);
                $bdate = $datehelper->date_from_mysql($bdate);
            } else {
                $bdate = null;
            }
        ?>      
        <?= Html::encode($bdate); ?>        
    </div>  
    <div class="mb-3 form-group">
        <label class="form-label" style="background:lightblue" for="client_avs"><?= $s->trans('sumex_ssn'); ?></label>
        <?= Html::encode($client->getClient_avs()); ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="client_insurednumber" class="form-label" style="background:lightblue"><?= $s->trans('sumex_insurednumber'); ?></label>
        <?= Html::encode($client->getClient_insurednumber()); ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="client_veka" class="form-label" style="background:lightblue"><?= $s->trans('sumex_veka'); ?></label>
        <?= Html::encode($client->getClient_veka()); ?>
    </div>
  </div>  
