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
<form id="InvModalForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
    <h1 class="headerbar-title"><?= $s->trans('invs_form'); ?></h1>
    <?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
    <?php echo (string)$response->getBody(); ?>
    <div id="content">
    <div class="row">
        <div "col-xs-12 col-sm-6 col-md-5">
                <div class="mb3 form-group" hidden>
                    <label for="inv_id">Invoice</label>
                    <input name="inv_id" id="inv_id" class="form-control" value="" hidden>                    
                </div>
                <div class="form-group has-feedback">
                <label for="create_inv_client_id"><?= $s->trans('client'); ?></label>
                <select name="client_id" id="create_inv_client_id" class="form-control simple-select" data-minimum-results-for-search="Infinity">
                    <?php foreach ($clients as $client) { ?>
                        <option value="<?= $client->getId(); ?>"
                            <?php $s->check_select($s->get_setting('default_invoice_group'), $client->getId()); ?>>
                            <?= Html::encode($client->client_name); ?>
                        </option>
                    <?php } ?>
                </select>
                </div>

                <div class="mb-3 form-group has-feedback"><?php  $date = $body['date_created'] ?? null; 
                if ($date && $date !== "0000-00-00") { 
                    $date = $datehelper->date_from_mysql($date); 
                } else { 
                    $date = null; 
                } 
                   ?>  
                <label form-label for="date_created"><?= $s->trans('date_created') ." (".  $datehelper->display().") "; ?></label>
                    <div class="mb3 input-group"> 
                        <input type="text" name="date_created" id="date_created" autocomplete="off" placeholder="<?= $datehelper->display(); ?>" 
                               class="form-control input-sm datepicker" 
                               value="<?php if ($date <> null) {echo Html::encode($date);} ?>"> 
                        <span class="input-group-text"> 
                        <i class="fa fa-calendar fa-fw"></i> 
                        </span> 
                    </div>
                </div>  

                <div class="mb3 form-group">
                    <label for="password"><?= $s->trans('invoice_pre_password'); ?></label>
                    <input type="text" name="password" id="password" class="form-control" style="display:none"
                  value="<?= Html::encode($body['password'] ??  ''); ?>">
                </div>

                <div class="mb3 form-group">
                    <label for="group_id"><?= $s->trans('invoice_group'); ?>: </label>
                    <select name="group_id" id="group_id"
                            class="form-control simple-select" data-minimum-results-for-search="Infinity">
                        <?php foreach ($groups as $group) { ?>
                            <option value="<?php echo $group->getId(); ?>"
                                <?= $s->check_select($s->get_setting('default_inv_group'), $group->getId()); ?>>
                                <?= Html::encode($group->name); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

            </div>


        </div>

    </div>

</div>
</form>
