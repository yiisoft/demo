<?php
declare(strict_types=1);

use Yiisoft\Html\Html;
use App\Invoice\Helpers\DateHelper;
?>

<!DOCTYPE html>
<html class="h-100" lang="<?= $s->trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
</head>    
<body>
<header class="clearfix">
    <div id="logo">
        <?php // echo invoice_logo_pdf(); ?>
    </div>
    <div id="client">
        <div>
            <b><?= Html::encode($inv->getClient()->getClient_name()); ?></b>
        </div>
        <?php if ($inv->getClient()->getClient_vat_id()) {
            echo '<div>' .$s->trans('vat_id_short') . ': ' . $inv->getClient()->getClient_vat_id() . '</div>';
        }
        if ($inv->getClient()->getClient_tax_code()) {
            echo '<div>' .$s->trans('tax_code_short') . ': ' . $inv->getClient()->getClient_tax_code() . '</div>';
        }
        if ($inv->getClient()->getClient_address_1()) {
            echo '<div>' . Html::encode($inv->getClient()->getClient_address_1()) . '</div>';
        }
        if ($inv->getClient()->getClient_address_2()) {
            echo '<div>' . Html::encode($inv->getClient()->getClient_address_2()) . '</div>';
        }
        if ($inv->getClient()->getClient_city() || $inv->getClient()->getClient_state() || $inv->getClient()->getClient_zip()) {
            echo '<div>';
            if ($inv->getClient()->getClient_city()) {
                echo Html::encode($inv->getClient()->getClient_city()) . ' ';
            }
            if ($inv->getClient()->getClient_state()) {
                echo Html::encode($inv->getClient()->getClient_state()) . ' ';
            }
            if ($inv->getClient()->getClient_zip()) {
                echo Html::encode($inv->getClient()->getClient_zip());
            }
            echo '</div>';
        }
        if ($inv->getClient()->getClient_state()) {
            echo '<div>' . Html::encode($inv->getClient()->getClient_state()) . '</div>';
        }
        if ($inv->getClient()->getClient_country()) {
            echo '<div>' . $countryhelper->get_country_name($s->trans('cldr'), $inv->getClient()->getClient_country()) . '</div>';
        }

        echo '<br/>';

        if ($inv->getClient()->getClient_phone()) {
            echo '<div>' .$s->trans('phone_abbr') . ': ' . Html::encode($inv->getClient()->getClient_phone()) . '</div>';
        } ?>

    </div>
    <div id="company">
        <?php 
        if (!empty($userinv)) {
            echo '<div><b>'.Html::encode($userinv->getName()).'</b></div>';
            echo '<div>' .$s->trans('vat_id_short') . ': ' . $userinv->getVat_id() . '</div>';
            echo '<div>' .$s->trans('tax_code_short') . ': ' . $userinv->getTax_code() . '</div>';
            echo '<div>' . Html::encode($userinv->getAddress_1() ?? '') . '</div>';
            echo '<div>' . Html::encode($userinv->getAddress_2() ?? '') . '</div>';
            echo '<div>';
            echo Html::encode($userinv->getCity() ?? '') . ' ';
            echo Html::encode($userinv->getState() ?? '') . ' ';
            echo Html::encode($userinv->getZip() ?? '');
            echo '</div>';
            echo '<div>' . get_country_name($s->trans('cldr'), $userinv->getCountry()) . '</div>';
            echo '<br/>';
            echo '<div>' .$s->trans('phone_abbr') . ': ' . Html::encode($userinv->getPhone() ?? '') . '</div>';
            echo '<div>' .$s->trans('fax_abbr') . ': ' . Html::encode($userinv->getFax() ?? '') . '</div>';
        }
        ?>
    </div>
</header>
<main>
    <div class="invoice-details clearfix">
        <table>
            <tr>
                <td><?php echo $s->trans('quote_date') . ':'; ?></td>
                    <?php
                        $date = $client->getClient_date_created();
                        if ($date && $date != "0000-00-00") {
                            //use the DateHelper
                            $datehelper = new DateHelper($s);
                            $date = $datehelper->date_from_mysql($date);
                        } else {
                            $date = null;
                        }
                    ?> 
                <td><?php echo Html::encode($date); ?></td>
            </tr>
            <tr>
                <td><?php echo $s->trans('expires') . ': '; ?></td>
                <?php
                        $date_due = $inv->getDate_due();
                        if ($date_due && $date_due != "0000-00-00") {
                            //use the DateHelper
                            $datehelper = new DateHelper($s);
                            $date_due_next = $datehelper->date_from_mysql($date_due);
                        } else {
                            $date_due_next = null;
                        }
                    ?> 
                <td><?php echo Html::encode($date_due_next); ?></td>
            </tr>
            <tr><?php echo $top_custom_fields; ?></tr>    
        </table>
    </div>

    <h3 class="invoice-title"><b><?php echo Html::encode($s->trans('quote') . ' ' . $inv->getNumber()); ?></b></h3>

    <table class="items table-primary table table-borderless no-margin">
        <thead style="display: none">
        <tr>
            <th class="item-name"><?= Html::encode($s->trans('item')); ?></th>
            <th class="item-desc"><?= Html::encode($s->trans('description')); ?></th>
            <th class="item-amount text-right"><?= Html::encode($s->trans('qty')); ?></th>
            <th class="item-price text-right"><?= Html::encode($s->trans('price')); ?></th>
            <?php if ($show_item_discounts) : ?>
                <th class="item-discount text-right"><?= Html::encode($s->trans('discount')); ?></th>
            <?php endif; ?>
            <th class="item-price text-right"><?= Html::encode($s->trans('tax')); ?></th>    
            <th class="item-total text-right"><?= Html::encode($s->trans('total')); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($items as $item) { 
            $inv_item_amount = $iiaR->repoInvItemAmountquery((string)$item->getId());
            ?>
            <tr>
                <td><?= Html::encode($item->getName()); ?></td>
                <td><?php echo nl2br(Html::encode($item->getDescription())); ?></td>
                <td class="text-right">
                    <?php echo Html::encode($s->format_amount($item->getQuantity())); ?>
                    <?php if ($item->getProduct_unit()) : ?>
                        <br>
                        <small><?= Html::encode($item->getProduct_unit()); ?></small>
                    <?php endif; ?>
                </td>
                <td class="text-right">
                    <?php echo Html::encode($s->format_currency($item->getPrice())); ?>
                </td>
                <?php if ($show_item_discounts) : ?>
                    <td class="text-right">
                        <?php echo Html::encode($s->format_currency($item->getDiscount_amount())); ?>
                    </td>
                <?php endif; ?>
                <td class="text-right">
                    <?php  
                        echo Html::encode($s->format_currency($inv_item_amount->getTax_total())); 
                    ?>
                </td>
                <td class="text-right">
                    <?php  
                        echo Html::encode($s->format_currency($inv_item_amount->getTotal())); 
                    ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
        <tbody class="invoice-sums">

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?>
                    class="text-right"><?= Html::encode($s->trans('subtotal'))." (".Html::encode($s->trans('price'))."-".Html::encode($s->trans('discount')).") x ".Html::encode($s->trans('qty')); ?></td>
            <td class="text-right"><?php echo Html::encode($s->format_currency($inv_amount->getItem_subtotal())); ?></td>
        </tr>

        <?php if ($inv_amount->getItem_tax_total() > 0) { ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?> class="text-right">
                    <?= Html::encode($s->trans('item_tax')); ?>
                </td>
                <td class="text-right">
                    <?php echo Html::encode($s->format_currency($inv_amount->getItem_tax_total())); ?>
                </td>
            </tr>
        <?php } ?>

            
        <?php if (!empty($inv_tax_rates)) { ?>    
        <?php  foreach ($inv_tax_rates as $inv_tax_rate) : ?>
            <tr>
                <td <?php echo ($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?> class="text-right">
                    <?php echo Html::encode($inv_tax_rate->getTaxRate()->getTax_rate_name()) . ' (' . Html::encode($s->format_amount($inv_tax_rate->getTaxRate()->getTax_rate_percent())) . '%)'; ?>
                </td>
                <td class="text-right">
                    <?php echo Html::encode($s->format_currency($inv_tax_rate->getInv_tax_rate_amount())); ?>
                </td>
            </tr>
        <?php endforeach ?>
        <?php } ?>    
        <?php if ($inv->getDiscount_percent() != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?> class="text-right">
                    <?= Html::encode($s->trans('discount')); ?>
                </td>
                <td class="text-right">
                    <?php echo Html::encode($s->format_amount($inv->getDiscount_percent())); ?>%
                </td>
            </tr>
        <?php endif; ?>
        <?php if ($inv->getDiscount_amount() != '0.00') : ?>
            <tr>
                <td <?php echo($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?> class="text-right">
                    <?= Html::encode($s->trans('discount')); ?>
                </td>
                <td class="text-right">
                    <?php echo Html::encode($s->format_currency($inv->getDiscount_amount())); ?>
                </td>
            </tr>
        <?php endif; ?>

        <tr>
            <td <?php echo($show_item_discounts ? 'colspan="6"' : 'colspan="5"'); ?> class="text-right">
                <b><?= Html::encode($s->trans('total')); ?></b>
            </td>
            <td class="text-right">
                <b><?php echo Html::encode($s->format_currency($inv_amount->getTotal())); ?></b>
            </td>
        </tr>
        </tbody>
    </table>

</main>

<footer>
    <?php if ($inv->getTerms()) : ?>
        <div class="notes">
            <b><?= Html::encode($s->trans('terms')); ?></b><br/>
            <?php echo nl2br(Html::encode($inv->getTerms())); ?>
        </div>
    <?php endif; ?>
    <?php if ($show_custom_fields) {
        echo $view_custom_fields;
    }
    ?>   
</footer>
</body>
</html>
