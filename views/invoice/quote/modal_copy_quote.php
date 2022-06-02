<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
// id="quote-to-quote" triggered by <a href="#quote-to-quote" data-toggle="modal"  style="text-decoration:none"> on views/quote/view.php line 92
?>
<div id="quote-to-quote" class="modal modal-lg" role="dialog" aria-labelledby="modal_quote_to_quote" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-body">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></button>
            </div>       
            <div class="modal-header">
                <h5 class="col-12 modal-title text-center"><?php echo $s->trans('copy_quote'); ?></h5>
                <br>
            </div> 
            <input type="hidden" name="user_id" id="user_id" value="<?= $quote->getUser_id(); ?>">
            <div class="form-group">
                <label for="create_quote_client_id"><?= $s->trans('client'); ?></label>
                <select name="create_quote_client_id" id="create_quote_client_id" class="form-control">
                    <option value="<?= $quote->getClient()->getClient_id(); ?>"><?= $quote->getClient()->getClient_name(); ?></option>
                        <?php foreach ($clients as $client) { ?>
                            <option value="<?= $client->getClient_id(); ?>">
                                <?= Html::encode($client->getClient_name()); ?>
                            </option>
                        <?php } ?>
                </select>          
            </div>
        </div>
        <div class="modal-footer">
            <div class="btn-group">
                <button class="quote_to_quote_confirm btn btn-success" id="quote_to_quote_confirm" type="button">
                    <i class="fa fa-check"></i> <?= $s->trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= $s->trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
