<?php
declare(strict_types=1); 
use Yiisoft\Html\Html;
?>
<div class="table-responsive">
    <table class="table table-hover table-bordered table-striped">
        <tr>
            <th>&nbsp;</th>
            <th><?= $s->trans('item'); ?></th>
            <th><?= $s->trans('product_sku'); ?></th>            
            <th><?= $s->trans('product_name'); ?></th>
            <th><?= $s->trans('product_description'); ?></th>
            <th class="text-right"><?= $s->trans('product_price'); ?></th>
            <th class="text-right"><?= $s->trans('quantity'); ?></th>
        </tr>
        <?php foreach ($invitems as $invitem) { ?>
            <tr class="product">
                <td class="text-left">
                    <input type="checkbox" name="item_ids[]" value="<?php echo $invitem->getId();?>">
                </td>
                <td nowrap class="text-left">
                    <b><?= Html::encode($invitem->getId()); ?></b>
                </td>
                <td nowrap class="text-left">
                    <b><?= Html::encode($invitem->getProduct()->getProduct_sku()); ?></b>
                </td>
                <td>
                    <b><?= Html::encode($invitem->getProduct()->getProduct_name()); ?></b>
                </td>
                <td>
                    <?= nl2br(Html::encode($invitem->getProduct()->getProduct_description())); ?>
                </td>
                <td class="text-right">
                    <?= $numberhelper->format_currency($invitem->getProduct()->getProduct_price()); ?>
                </td>
                <td class="text-right">
                    <?= $invitem->getQuantity(); ?>
                </td>
            </tr>
        <?php } ?>

    </table>
</div>
