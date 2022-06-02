<?php
declare(strict_types=1);
// id="modal-change-client" triggered by <a href="#modal-change-client"> on inv\view

?>
<div id="modal-change-client" class="modal modal-lg" role="dialog" aria-labelledby="modal_change_client" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></button>
        </div>
        <div class="modal-header">
            <h4 class="panel-title"><?= $s->trans('change_client'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="change_client_id"><?= $s->trans('client'); ?></label>
                <select name="change_client_id" id="change_client_id" class="form-control">
                    <option value="0"><?= $s->trans('none'); ?></option>
                        <?php foreach ($clients as $client) { ?>
                            <option value="<?= $client->getClient_id(); ?>">
                                <?= $client->getClient_name(); ?>
                            </option>
                        <?php } ?>
                </select>
            </div>
            <input class="hidden" id="inv_id" value="<?= $inv->getId(); ?>">
        </div>
        <div class="modal-header">
            <div class="btn-group">
                <button class="client_change_confirm btn btn-success" id="client_change_confirm" type="button">
                    <i class="fa fa-check"></i> <?= $s->trans('submit'); ?>
                </button>
            </div>
        </div>
    </form>
</div>
