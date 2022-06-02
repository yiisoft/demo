<?php
declare(strict_types=1);

use Yiisoft\Html\Html;
// id="quote-to-invoice" triggered by <a href="#quote-to-invoice" data-toggle="modal"  style="text-decoration:none"> on views/quote/view.php line 86
?>
<div id="quote-to-invoice" class="modal modal-lg" role="dialog" aria-labelledby="modal_quote_to_invoice" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-body">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></button>
            </div>       
            <div class="modal-header">
                <h5 class="col-12 modal-title text-center"><?php echo $s->trans('quote_to_invoice'); ?></h5>
                <br>
            </div>
            <input type="hidden" name="client_id" id="client_id" value="<?php echo $quote->getClient_id(); ?>">            
            <input type="hidden" name="user_id" id="user_id" value="<?php echo $quote->getUser_id(); ?>">
            <div class="form-group">
                <label for="password"><?= $s->trans('invoice_password'); ?></label>
                <input type="text" name="password" id="invoice_password" class="form-control"
                       value="<?= $s->setting('invoice_pre_password') == '' ? '' : $s->setting('invoice_pre_password') ?>"
                       autocomplete="off">
            </div>
            <div class="form-group">
                <label for="group_id">
                    <?= $s->trans('invoice_group'); ?>
                </label>
                <select name="group_id" id="group_id" class="form-control">
                    <?php foreach ($groups as $group) { ?>
                        <option value="<?= $group->getId(); ?>"
                            <?= $s->check_select($s->setting('default_invoice_group'), $group->getId()); ?>>
                            <?= Html::encode($group->getName()); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <div class="btn-group">
                <button class="quote_to_invoice_confirm btn btn-success" id="quote_to_invoice_confirm" type="button">
                    <i class="fa fa-check"></i> <?= $s->trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= $s->trans('cancel'); ?>
                </button>
            </div>
        </div>
    </form>
</div>