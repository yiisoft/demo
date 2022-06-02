<?php
    declare(strict_types=1);
?>
<div class="headerbar-item pull-right">
    <div class="btn-group btn-group-sm">
        <?php if (!$hide_submit_button) : ?>
            <button id="btn-submit" name="btn_submit" class="btn btn-success ajax-loader" value="1">
                <i class="fa fa-check"></i> <?= $s->trans('save'); ?>
            </button>
        <?php endif; ?>
        <?php if (!$hide_cancel_button) : ?>
            <button type="button" onclick="window.history.back()" id="btn-cancel" name="btn_cancel" class="btn btn-danger" value="1">
                <i class="fa fa-arrow-left"></i> <?= $s->trans('back'); ?>
            </button>
        <?php endif; ?>
    </div>
</div>
