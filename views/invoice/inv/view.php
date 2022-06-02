<?php
declare(strict_types=1);

use Yiisoft\Html\Html;
use App\Invoice\Helpers\ClientHelper;
use App\Invoice\Helpers\CountryHelper;
use App\Invoice\Helpers\DateHelper;
use Yiisoft\Yii\Bootstrap5\Alert;


/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var string $title 
 * @var \Yiisoft\Session\Flash\FlashInterface $flash_interface
 */

?>
<div class="panel panel-default">
<div class="panel-heading">
    <?= $s->trans('invoice'); ?>
</div>
    <?php
        $clienthelper = new ClientHelper();
        $countryhelper = new CountryHelper();          
        echo $modal_delete_inv; 
        echo $modal_add_inv_tax; 
        echo $modal_change_client;  
        // modal_product_lookups is performed using below $modal_choose_items
        echo $modal_choose_items;
        echo $modal_inv_to_pdf;
        echo $modal_copy_inv;
        echo $modal_delete_items;
        echo $modal_create_recurring;
        echo $modal_create_credit;
    ?>
<div>
<br>
<br>
</div>    
<div>
    <?php 
        // line 845: InvController
        echo $add_inv_item; 
    ?>
</div> 
<input type="hidden" id="_csrf" name="_csrf" value="<?= $csrf ?>">   
<div id="headerbar">
    <h1 class="headerbar-title">
    <?php
        echo $s->trans('invoice') . ' ';
        echo($inv->getNumber() ? '#' . $inv->getNumber() :  $inv->getId());
    ?>
    </h1>
    <div class="headerbar-item pull-right <?php if ($inv->getIs_read_only() != 1 || $inv->getStatus_id() != 4) { ?>btn-group<?php } ?>">
        <div class="options btn-group">
            <a class="btn btn-default" data-toggle="dropdown" href="#">
                <i class="fa fa-chevron-down"></i><?= $s->trans('options'); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a href="<?= $urlGenerator->generate('inv/edit',['id'=>$inv->getId()]) ?>" style="text-decoration:none">
                        <i class="fa fa-edit fa-margin"></i>
                        <?= $s->trans('edit'); ?>
                    </a>
                </li>
                <li>
                    <a href="#add-inv-tax" data-toggle="modal"  style="text-decoration:none">
                        <i class="fa fa-plus fa-margin"></i>
                        <?= $s->trans('add_invoice_tax'); ?>
                    </a>
                </li>
                <li>
                    <a href="#create-credit-inv" data-toggle="modal" data-invoice-id="<?= $inv->getId(); ?>" style="text-decoration:none">
                        <i class="fa fa-minus fa-margin"></i> <?= $s->trans('create_credit_invoice'); ?>
                    </a>
                </li>
                <?php 
                $inv_amount = ($iaR->repoInvAmountcount($inv->getId()) > 0 ? $iaR->repoInvquery($inv->getId()) : '');
                if (!empty($inv_amount) && $inv_amount->getBalance() > 0) : ?>
                    <li>
                        <a href="<?= $urlGenerator->generate('payment/add'); ?>" style="text-decoration:none" class="invoice-add-payment"
                           data-invoice-id="<?= $inv->getId(); ?>"
                           data-invoice-balance="<?= $inv_amount->getBalance() ?? 0.00; ?>"
                           data-invoice-payment-method="<?= $inv->getPayment_method(); ?>"
                           data-payment-cf-exisst="<?= $payment_cf_exist; ?>">
                           <i class="fa fa-credit-card fa-margin"></i>
                           <?= $s->trans('enter_payment'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="#inv-to-pdf"  data-toggle="modal" style="text-decoration:none">
                        <i class="fa fa-print fa-margin"></i>
                        <?= $s->trans('download_pdf'); ?>
                        <!-- 
                            views/invoice/inv/modal_inv_to_pdf   ... include custom fields or not on pdf
                            src/Invoice/Inv/InvController/pdf ... calls the src/Invoice/Helpers/PdfHelper->generate_inv_pdf
                            src/Invoice/Helpers/PdfHelper ... calls the src/Invoice/Helpers/MpdfHelper->pdf_create
                            src/Invoice/Helpers/MpdfHelper ... saves folder in src/Invoice/Uploads/Archive
                            using 'pdf_invoice_template' setting or 'default' views/invoice/template/invoice/invoice.pdf
                        -->
                    </a>
                </li>
                <li>
                    <a href="#create-recurring-inv" data-toggle="modal"  style="text-decoration:none">
                        <i class="fa fa-refresh fa-margin"></i>
                        <?= $s->trans('create_recurring'); ?>
                    </a>
                </li>
                <li>
                    <a href=""  style="text-decoration:none">
                        <i class="fa fa-send fa-margin"></i>
                        <?= $s->trans('send_email'); ?>
                    </a>
                </li>
                <li>                    
                    <a href="#inv-to-inv" data-toggle="modal"  style="text-decoration:none">
                        <i class="fa fa-copy fa-margin"></i>
                         <?= $s->trans('copy_invoice'); ?>
                    </a>
                </li>
                <li>
                    <a href="#delete-inv" data-toggle="modal"  style="text-decoration:none">
                        <i class="fa fa-trash fa-margin"></i> <?= $s->trans('delete'); ?>
                    </a>
                </li>
                <li>      
                    <a href="#delete-items"  data-toggle="modal" style="text-decoration:none">
                        <i class="fa fa-trash fa-margin"></i>
                        <?= $s->trans('delete')." ".$s->trans('item'); ?>
                    </a>
                </li>
            </ul>
        </div>        
    </div>
</div>

<div id="content">    
<?= $alert; ?>
    <div id="inv_form">
        <div class="inv">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-5">
                    <h3>
                        <a href="<?= $urlGenerator->generate('client/view',['id' => $inv->getClient()->getClient_id()]); ?>">
                            <?= Html::encode($clienthelper->format_client($inv->getClient())) ?>
                        </a>
                    </h3>
                    <br>
                    <div id="pre_save_client_id" value="<?php echo $inv->getClient()->getClient_id(); ?>" hidden></div>
                    <div class="client-address">
                        <span class="client-address-street-line-1">
                            <?php echo($inv->getClient()->getClient_address_1() ? Html::encode($inv->getClient()->getClient_address_1()) . '<br>' : ''); ?>
                        </span>
                        <span class="client-address-street-line-2">
                            <?php echo($inv->getClient()->getClient_address_2() ? Html::encode($inv->getClient()->getClient_address_2()) . '<br>' : ''); ?>
                        </span>
                        <span class="client-address-town-line">
                            <?php echo($inv->getClient()->getClient_city() ? Html::encode($inv->getClient()->getClient_city()) . '<br>' : ''); ?>
                            <?php echo($inv->getClient()->getClient_state() ? Html::encode($inv->getClient()->getClient_state()) . '<br>' : ''); ?>
                            <?php echo($inv->getClient()->getClient_zip() ? Html::encode($inv->getClient()->getClient_zip()) : ''); ?>
                        </span>
                        <span class="client-address-country-line">
                            <?php echo($inv->getClient()->getClient_country() ? '<br>' . $countryhelper->get_country_name($s->trans('cldr'), $inv->getClient()->getClient_country()) : ''); ?>
                        </span>
                    </div>
                    <hr>
                    <?php if ($inv->getClient()->getClient_phone()): ?>
                        <div class="client-phone">
                            <?= $s->trans('phone'); ?>:&nbsp;
                            <?= Html::encode($inv->getClient()->getClient_phone()); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($inv->getClient()->getClient_mobile()): ?>
                        <div class="client-mobile">
                            <?= $s->trans('mobile'); ?>:&nbsp;
                            <?= Html::encode($inv->getClient()->getClient_mobile()); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($inv->getClient()->getClient_email()): ?>
                        <div class='client-email'>
                            <?= $s->trans('email'); ?>:&nbsp;
                            <?php echo $inv->getClient()->getClient_email(); ?>
                        </div>
                    <?php endif; ?>
                    <br>
                </div>

                <div class="col-xs-12 visible-xs"><br></div>

                <div class="col-xs-12 col-sm-6 col-md-7">
                    <div class="details-box">
                        <div class="row">

                            <div class="col-xs-12 col-md-6">

                                <div class="invoice-properties">
                                    <label for="inv_number">
                                        <?= $s->trans('invoice'); ?> #
                                    </label>
                                    <input type="text" id="inv_number" class="form-control input-sm" readonly
                                        <?php if ($inv->getNumber()) : ?> value="<?php echo $inv->getNumber(); ?>"
                                        <?php else : ?> placeholder="<?= $s->trans('not_set'); ?>"
                                        <?php endif; ?>>
                                </div>
                                <div class="invoice-properties has-feedback">
                                    <label for="date_created">
                                        <?= $s->trans('date'); ?>
                                    </label>
                                    <div class="input-group">
                                        <input id="inv_date_created" disabled
                                               class="form-control input-sm datepicker"  
                                               <?php $dc_datehelper = new DateHelper($s); ?>
                                               value="<?php echo $dc_datehelper->date_from_mysql($inv->getDate_created()); ?>"/>
                                        <span class="input-group-text">
                                            <i class="fa fa-calendar fa-fw"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="invoice-properties has-feedback">
                                    <label for="inv_date_due">
                                        <?= $s->trans('expires'); ?>
                                    </label>
                                    <div class="input-group">
                                        <input name="inv_date_due" id="inv_date_due" disabled
                                               class="form-control input-sm datepicker"
                                               value="<?php echo $datehelper->date_from_mysql($inv->getDate_due()); ?>">
                                        <span class="input-group-text">
                                            <i class="fa fa-calendar fa-fw"></i>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <?php foreach ($custom_fields as $custom_field): ?>
                                        <?php if ($custom_field->getLocation() !== 1) {continue;} ?>
                                        <?php  $cvH->print_field_for_view($inv_custom_values, $custom_field, $custom_values); ?>                                   
                                    <?php endforeach; ?>
                                </div>    
                            </div>
                            <div class="col-xs-12 col-md-6">

                                <div class="invoice-properties">
                                    <label for="inv_status_id">
                                        <?= $s->trans('status'); ?>
                                    </label>
                                    <select name="inv_status_id" id="inv_status_id" disabled
                                            class="form-control">
                                        <?php foreach ($inv_statuses as $key => $status) { ?>
                                            <option value="<?php echo $key; ?>" <?php if ($key === $body['status_id']) {  $s->check_select(Html::encode($body['status_id'] ?? ''), $key);} ?>>
                                                <?= Html::encode($status['label']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="invoice-properties">
                                    <label><?= $s->trans('payment_method'); ?></label>
                                    <select name="payment_method" id="payment_method"
                                            class="form-control"
                                        <?php if ($inv->getIs_read_only() == 1 && $inv->getStatus_id() == 4) {
                                            echo 'disabled="disabled"';
                                        } ?>>
                                        <option value="0"><?= $s->trans('select_payment_method'); ?></option>
                                        <?php foreach ($payment_methods as $payment_method) { ?>
                                            <option <?php $s->check_select($inv->getPayment_method(),
                                                $payment_method->getId()) ?>
                                                value="<?= $payment_method->getId(); ?>">
                                                <?= $payment_method->getName(); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="invoice-properties">
                                    <label for="inv_password"><?php echo $s->trans('password'); ?></label>
                                    <input type="text" id="inv_password" class="form-control input-sm" disabled value="<?= Html::encode($body['password'] ?? ''); ?>">
                                </div>                                
                                <?php if ($inv->getStatus_id() != 1) { ?>
                                <div class="invoice-properties">
                                    <div class="form-group">
                                        <label for="guest-url"><?php echo $s->trans('guest_url'); ?></label>
                                        <div class="input-group">
                                            <input type="text" id="guest-url" name="guest-url" readonly class="form-control" value="<?= $inv->getUrl_key(); ?>">
                                            <span class="input-group-text to-clipboard cursor-pointer"
                                                  data-clipboard-target="#guest-url">
                                                <i class="fa fa-clipboard fa-fw"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <input type="text" id="dropzone_client_id" readonly class="form-control" value="<?=  $inv->getClient()->getClient_id(); ?>" hidden>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   <div id="partial_item_table_parameters" inv_items="<?php $inv_items; ?>" disabled>
    <?php
       echo $partial_item_table;
    ?>     
   </div>
    
   <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="panel panel-default no-margin">
                    <div class="panel-heading">
                        <?= $s->trans('terms'); ?>
                    </div>
                    <div class="panel-body">
                        <textarea name="terms" id="terms" rows="3" disabled
                                  class="input-sm form-control"><?= Html::encode($body['terms'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="col-xs-12 visible-xs visible-sm"><br></div>

            </div>
            <div id="view_custom_fields" class="col-xs-12 col-md-6">
                <?php //echo $dropzone_inv_html; ?>
                <?php echo $view_custom_fields; ?>
            </div>
    </div>
</div>
</div>    
<div>     
<?php
     $js38 = "$(function () {".
        '$(".form-control.input-sm.datepicker").datepicker({dateFormat:"'.$datehelper->datepicker().'"});'.
      '});';
      echo Html::script($js38)->type('module');
?>
</div>

