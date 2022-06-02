<?php
declare(strict_types=1);
// delete-items triggered by #delete-items on quote\view.php
?>
<div id="delete-items" class="modal modal-lg" role="dialog" aria-labelledby="modal_delete_item" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-body">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></button>
            </div>       
            <div class="modal-header">
                <h5 class="col-12 modal-title text-center"><?= $s->trans('delete')." ".$s->trans('item'); ?></h5>
                <br>
            </div>    
        </div>
        <div>
            <?php echo $partial_item_table_modal; ?>
        </div>    
        <div class="modal-footer">
            <div class="btn-group">
                <!--quote.js delete-items-confirm-quote function  -->
                <button class="delete-items-confirm-quote btn btn-success" id="delete-items-confirm-quote" type="button">
                            <i class="fa fa-check"></i><?= $s->trans('yes'); ?>
                </button>                
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?= $s->trans('cancel'); ?>
                </button>
            </div>
        </div>
    </form>
</div>