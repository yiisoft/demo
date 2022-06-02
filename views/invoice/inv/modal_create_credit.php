<?php
    declare(strict_types=1);
    use Yiisoft\Html\Html;
?>
<div id="create-credit-inv" class="modal modal-lg" role="dialog" aria-labelledby="modal-create-credit-invoice"
     aria-hidden="true">
    <form class="modal-content">
        <div class="modal-body">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            </div>
            <div class="modal-header">               
                <h4 class="col-12 modal-title text-center"><?= $s->trans('create_credit_invoice'); ?></h4>
                <br>
            </div>  
        </div>    
        <div class="modal-body">
            <input type="hidden" name="user_id" id="user_id" class="form-control"
                   value="<?= $inv->getUser_id(); ?>">

            <input type="hidden" name="parent_id" id="parent_id"
                   value="<?= $inv->getId(); ?>">

            <input type="hidden" name="client_id" id="client_id" class="hidden"
                   value="<?= $inv->getClient_id(); ?>">

            <input type="hidden" name="inv_date_created" id="inv_date_created"
                   value="<?= $credit_date = $datehelper->date_from_mysql(new DateTimeImmutable('now')); echo $credit_date; ?>">

            <div class="form-group">
                <label for="inv_password"><?= $s->trans('invoice_password'); ?></label>
                <input type="text" name="inv_password" id="inv_password" class="form-control"
                       value="<?= $s->get_setting('invoice_pre_password') == '' ? '' : $s->get_setting('invoice_pre_password'); ?>"
                       style="margin: 0 auto;" autocomplete="off">
            </div>

            <div>
                <select name="inv_group_id" id="inv_group_id" class="hidden">
                    <?php foreach ($invoice_groups as $invoice_group) { ?>
                        <option value="<?= $invoice_group->getId(); ?>"
                            <?php if ($s->get_setting('default_invoice_group') === $invoice_group->getId()) {
                                echo 'selected="selected"';
                                $credit_invoice_group = Html::encode($invoice_group->getName());
                            } ?>>
                            <?= $credit_invoice_group; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <p><strong><?= $s->trans('credit_invoice_details'); ?></strong></p>

            <ul>
                <li><?= $s->trans('client') . ': ' . Html::encode($inv->getClient()->getClient_name()); ?></li>
                <li><?= $s->trans('credit_invoice_date') . ': ' . $credit_date; ?></li>
                <li><?= $s->trans('invoice_group') . ': ' . $credit_invoice_group; ?></li>
            </ul>

            <div class="alert alert-danger no-margin">
                <?= $s->trans('create_credit_invoice_alert'); ?>
            </div>

        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="create-credit-confirm btn btn-success" id="create-credit-confirm" type="button">
                    <i class="fa fa-check"></i> <?= $s->trans('confirm'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= $s->trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
