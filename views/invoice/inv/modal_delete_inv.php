<?php

declare(strict_types=1); 

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var string $csrf
 * @var string $action
 */

// id="delete-inv" triggered by <a href="#delete-inv" data-toggle="modal"  style="text-decoration:none"> on views/inv/view.php

?>

<div id="delete-inv" class="modal modal-lg" role="dialog" aria-labelledby="modal_delete_inv" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?= $s->trans('delete_invoice'); ?></h4>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger"><?= $s->trans('delete_invoice_warning'); ?></div>
        </div>
        <div class="modal-footer">
            <form action="<?= $urlGenerator->generate(...$action) ?>" method="POST">
                <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                <div class="btn-group">
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash-o fa-margin"></i> <?= $s->trans('confirm_deletion') ?>
                    </button>
                    <a href="#" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> <?= $s->trans('cancel'); ?>
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>

