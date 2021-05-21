<?php
   use frontend\modules\invoice\application\components\Utilities; 
   use yii\helpers\Url;
?>
<div class="sidebar hidden-xs">
    <ul>
        <li hidden>
            <a href="<?php //Url::toRoute([]); ?>" title="<?= Utilities::trans('clients'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-users"></i>
            </a>
        </li>
        <li hidden>
            <a href="<?php //Url::toRoute([]);  ?>" title="<?= Utilities::trans('quotes'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-file"></i>
            </a>
        </li>
        <li>
            <a href="<?= Url::to(['/invoice/salesinvoice']);  ?>" title="<?= Utilities::trans('invoices'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-file-text"></i>
            </a>
        </li>
        <li>
            <a href="<?= Url::to(['/invoice/salesinvoicepayment']);  ?>" title="<?= Utilities::trans('payments'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-money"></i>
            </a>
        </li>
        <li hidden>
            <a href="<?php //Url::toRoute([]);  ?>" title="<?= Utilities::trans('products'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-database"></i>
            </a>
        </li>
        <?php if ($mdl_settings->get_setting('projects_enabled') == 1) : ?>
            <li hidden>
                <a href="<?php //Url::toRoute([]);  ?>" title="<?= Utilities::trans('tasks'); ?>"
                   class="tip" data-placement="right">
                    <i class="fa fa-check-square-o"></i>
                </a>
            </li>
        <?php endif; ?>
        <li>
            <a href="<?= Url::to(['/invoice/ip/settings']);  ?>" title="<?= Utilities::trans('system_settings'); ?>"
               class="tip" data-placement="right">
                <i class="fa fa-cogs"></i>
            </a>
        </li>
    </ul>
</div>
