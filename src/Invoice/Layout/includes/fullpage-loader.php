<?php
  use frontend\modules\invoice\application\components\Utilities;
?>

<div id="fullpage-loader" style="display: none">
    <div class="loader-content">
        <i id="loader-icon" class="fa fa-cog fa-spin"></i>
        <div id="loader-error" style="display: none">
            <?= Utilities::trans('loading_error'); ?><br/>
            <a href="https://wiki.invoiceplane.com/<?= Utilities::trans('cldr'); ?>/1.0/general/faq"
               class="btn btn-primary btn-sm" target="_blank">
                <i class="fa fa-support"></i> <?= Utilities::trans('loading_error_help'); ?>
            </a>
        </div>
    </div>
    <div class="text-right">
        <button type="button" class="fullpage-loader-close btn btn-link tip" aria-label="<?= Utilities::trans('close'); ?>"
                title="<?= Utilities::trans('close'); ?>" data-placement="left">
            <span aria-hidden="true"><i class="fa fa-close"></i></span>
        </button>
    </div>
</div>
