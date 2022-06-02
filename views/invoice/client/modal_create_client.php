<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\View\WebView $this
 */

// id="create-client" triggered by <a href="#create-client" class="btn btn-success" data-toggle="modal"  style="text-decoration:none"> on 
// views/client/index.php

?>
<div id="create-client" class="modal modal-lg" role="dialog" aria-labelledby="modal_create_client" aria-hidden="true">
    <form class="modal-content">
      <div class="modal-body">  
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></button>
        </div>        
        <div class="modal-header">
            <h5 class="col-12 modal-title text-center"><?php echo $s->trans('create_client'); ?></h5>
            <br>
        </div>
        <div class="mb-3 form-group">
            <label for="client_name" class="form-label"><?= $s->trans('client_name'); ?><span style="color:red">*</span></label>
            <input type="text" class="form-control" name="client_name" id="client_name" placeholder="<?= $s->trans('client_name'); ?>" value="<?= Html::encode($body['client_name'] ?? '') ?>" required>
        </div>
        <div class="mb-3 form-group">
            <label for="client_surname" class="form-label"><?= $s->trans('client_surname'); ?></label>
            <input type="text" class="form-control" name="client_surname" id="client_surname" placeholder="<?= $s->trans('client_surname'); ?>" value="<?= Html::encode($body['client_surname'] ?? '') ?>">
        </div>
        <div class="mb-3 form-group">
            <label for="client_email" class="form-label"><?= $s->trans('email'); ?><span style="color:red">*</span></label>
            <input type="text" class="form-control" name="client_email" id="client_email" placeholder="<?= $s->trans('email'); ?>" value="<?= Html::encode($body['client_email'] ?? '') ?>" required>
        </div>
        <div class="modal-header">
            <div class="btn-group">
                <button class="client_create_confirm btn btn-success" id="client_create_confirm" type="button">
                    <i class="fa fa-check"></i>
                    <?= $s->trans('submit'); ?>
                </button>
            </div>
        </div>
      </div>    
    </form>
</div>
