<?php

declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use App\Invoice\Helpers\DateHelper;
use DateTimeImmutable;
use App\Invoice\Helpers\ModalHelper;

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var string $title
 */

$datehelper = new DateHelper($s);

if (!empty($errors)) {
    foreach ($errors as $field => $error) {
        echo Alert::widget()->options(['class' => 'alert-danger'])->body(Html::encode($field . ':' . $error));
    }
}

?>
<form class="row" id="InvForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
    <div id="headerbar">
        <h1 class="headerbar-title"><?= $s->trans('invoices_form'); ?></h1>    
        <?php
            $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]);
            echo (string)$response->getBody();
        ?>
    </div>
    <div class="form-group">
        <div class="col-xs-12 col-sm-2 text-right text-left-xs">
            <label for="number"><?= $s->trans('invoice');?></label>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">  
                <input type="text" name="number" id="number" class="form-control" required disabled value="<?= Html::encode($body['number'] ??  ''); ?>">
            </div>
        </div>
    </div>    
    <div class="form-group">
        <div class="col-xs-12 col-sm-2 text-right text-left-xs">
            <label for="client_id"><?= $s->trans('client'); ?></label>
        </div>        
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">  
                <select name="client_id" id="client_id" class="form-control" required>
                 <option value=""><?= $s->trans('client'); ?></option>
                   <?php foreach ($clients as $client) { ?>
                    <option value="<?= $client->getClient_id(); ?>"
                     <?php $s->check_select(Html::encode($body['client_id'] ?? ''), $client->getClient_id()) ?>
                     ><?= $client->getClient_name(); ?></option>
                   <?php } ?>     
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-12 col-sm-2 text-right text-left-xs">
            <label for="group_id"><?= $s->trans('invoice_group'); ?>: </label>
        </div>        
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">  
                <select name="group_id" id="group_id"
                        class="form-control">
                    <?php foreach ($groups as $group) { ?>
                        <option value="<?php echo $group->getId(); ?>"
                            <?= $s->check_select(Html::encode($body['group_id'] ?? ''), $group->getId()); ?>>
                            <?= Html::encode($group->getName()); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>    
    </div>
    <div class="form-group">
        <div class="col-xs-12 col-sm-2 text-right text-left-xs">
            <label for="creditinvoice_parent_id"></label>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">  
                <input type="text" name="creditinvoice_parent_id" id="creditinvoice_parent_id" class="form-control" hidden value="<?= Html::encode($body['creditinvoice_parent_id'] ??  0); ?>">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-12 col-sm-2 text-right text-left-xs">
            <label form-label for="date_created"><?= $s->trans('created') ." (".  $datehelper->display().") "; ?></label>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">  
                <input type="text" name="date_created" disabled id="date_created" placeholder="<?= $datehelper->display(); ?>" 
                   class="form-control input-sm datepicker" 
                   value="<?= Html::encode($datehelper->date_from_mysql($body['date_created'] ?? new DateTimeImmutable('now'))); ?>"> 
                <span class="input-group-text"> 
                    <i class="fa fa-calendar fa-fw"></i> 
                </span> 
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-12 col-sm-2 text-right text-left-xs">
            <label for="password"><?= $s->trans('password'); ?></label>
        </div>        
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">  
                <input type="text" name="password" id="password" class="form-control" value="<?= Html::encode($body['password'] ??  ''); ?>">
            </div>
        </div>    
    </div>
    <div class="form-group">
        <div class="col-xs-12 col-sm-2 text-right text-left-xs">
            <label for="status_id">
                <?php echo $s->trans('status'); ?>
            </label>
        </div>                
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">  
            <select name="status_id" id="status_id" class="form-control">
                <option value="0"><?php Html::encode($body['status_id'] ?? 1); ?></option>
                <?php foreach ($inv_statuses as $key => $status) { ?>
                    <option value="<?php echo $key; ?>" <?php $s->check_select(Html::encode($body['status_id'] ?? ''), $key) ?>>
                        <?php echo $status['label']; ?>
                    </option>
                <?php } ?>
            </select>
            </div>
        </div>    
    </div>

    <div class="form-group">
        <div class="col-xs-12 col-sm-2 text-right text-left-xs">
            <label for="url_key">
                <?= ($body['status_id'] ?? 1) > 1 ? $s->trans('guest_url') : ''; ?>
            </label>
        </div>                        
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">  
                <input type="text" name="url_key" id="url_key" class="form-control" readonly value="<?= Html::encode($body['url_key'] ??  ''); ?>" <?= ($body['status_id'] ?? 1) == 1 ? 'hidden' : ''; ?>>
            </div>
        </div>    
    </div>

    <div class="form-group">
        <div class="col-xs-12 col-sm-2 text-right text-left-xs">
            <label for="discount_amount"><?= $s->trans('discount'); ?></label>
        </div>                                
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">  
                <input type="number" name="discount_amount" id="discount_amount" class="form-control" 
                    value="<?= $s->format_amount($body['discount_amount'] ?? ''); ?>">
                    <span class="input-group-text"><?= $s->get_setting('currency_symbol'); ?></span>
            </div>    
        </div>
    </div>

    <div class="form-group">        
        <div class="col-xs-12 col-sm-2 text-right text-left-xs">
            <label for="discount_percent"><?= $s->trans('discount'); ?></label>
        </div>                                
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">
                <input type="number" name="discount_percent" id="discount_percent" class="form-control"
                    value="<?= $s->format_amount($body['discount_percent'] ?? ''); ?>">
                    <span class="input-group-text">&percnt;</span>
            </div>
        </div>    
    </div>

    <div class="form-group">        
        <div class="col-xs-12 col-sm-2 text-right text-left-xs">
            <label for="terms"><?= $s->trans('terms'); ?></label>
        </div>                                        
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">
                <input type="text" name="terms" id="terms" class="form-control" value="<?= Html::encode($body['terms'] ??  ''); ?>">
            </div>
        </div>    
    </div>

    <?php foreach ($custom_fields as $custom_field): ?>
        <div class="form-group">
        <?= $cvH->print_field_for_form($inv_custom_values,
                                       $custom_field,
                                       // Custom values to fill drop down list if a dropdown box has been created
                                       $custom_values, 
                                       // Class for div surrounding input
                                       'col-xs-12 col-sm-6',
                                       // Class surrounding above div
                                       'form-group',
                                       // Label class similar to above
                                       'control-label'); ?>
        </div>    
    <?php endforeach; ?>


    <div class="form-group">
        <div class="col-xs-12 col-sm-2 text-right text-left-xs">   
            <label for="id" class="control-label"></label>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="input-group">
                <input type="hidden" name="id" id="id" class="form-control" value="<?= Html::encode($body['id'] ??  ''); ?>">
            </div>
        </div>    
    </div>

<?php $js109 = "$(function () {".
        '$(".form-control.input-sm.datepicker").datepicker({dateFormat:"'.$datehelper->datepicker().'"});'.
      '});';
      echo Html::script($js109)->type('module');
?>
</form>