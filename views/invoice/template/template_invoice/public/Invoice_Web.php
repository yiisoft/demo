<?php
   use frontend\modules\invoice\assets\InvoiceThemeStyleCssAsset;   
   use frontend\modules\invoice\application\components\Utilities;
   use frontend\modules\invoice\application\helpers\InvoiceHelper;
   use frontend\modules\invoice\application\helpers\DateHelper;
   use yii\helpers\Html;
   use yii\helpers\Url;
   use frontend\models\Salesorderheader;
   use frontend\widgets\Alert;
   InvoiceThemeStyleCssAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo Utilities::trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>
        <?php echo Html::encode($this->context->mdl_settings->get_setting('custom_title', 'Invoice', true)); ?>
        - <?php echo Utilities::trans('invoice'); ?><?php echo $model->invoice_id; ?>
    </title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <?php
       $this->head();
    ?>
</head>
<body>
<div class="container">
    <div id="content">
        <div class="webpreview-header">
            <h2><?php echo Utilities::trans('invoice'); ?>&nbsp;<?php echo $model->invoice_id; ?></h2>
            <div class="btn-group">
                    <a href="<?= Url::toRoute(['@web/invoice/view/pdf', 'invoice_url_key' => $model->invoice_url_key]); ?>"class="btn btn-primary">                    
                        <i class="fa fa-print"></i> <?php echo Utilities::trans('download_pdf'); ?>
                    </a>
                    <?php if ($this->context->mdl_settings->get_setting('enable_online_payments') == 1 && $balance->invoice_balance > 0) { ?>
                        <a href="<?= Url::toRoute(['@web/invoice/paymentinformation/form', 'invoice_url_key' => $model->invoice_url_key]); ?>"
                           class="btn btn-success">
                            <i class="fa fa-credit-card"></i> <?php echo Utilities::trans('pay_now'); ?>
                        </a>
                    <?php } ?>
            </div>
        </div>

        <hr>

        <?= Alert::widget() ?>
        
        <div class="invoice">

            <?php
            $logo = InvoiceHelper::invoice_logo();
            if ($logo) { ?>
                <img src="<?= Yii::$app->request->baseUrl.Utilities::getPlaceholderRelativeUrl().InvoiceHelper::invoice_logo();?>" height="60" width="60">
            <?php } ?>
            <br>  
            <br>
            <?php
                $pdf_invoice_footer = '';
                $url=Url::toRoute(['company/update', 'id'=>$company->id]);
                echo Html::a(Yii::t('app','Company Address:'), $url,['class' => 'link']);
                //$mdl_settings = new Mdl_settings();
                //$mdl_settings->load_settings();
                if (!empty($this->context->mdl_settings->get_setting('pdf_invoice_footer','',false))){
                $pdf_invoice_footer = $this->context->mdl_settings->get_setting('pdf_invoice_footer','',false);}
                $sum_unit_price = 0;
                $after = '';$before='';$afterspace = '';$currency_symbol='';$currency_symbol_placement='';
                $currency_symbol = $this->context->mdl_settings->get_setting('currency_symbol','',false);
                $currency_symbol_placement = $this->context->mdl_settings->get_setting('currency_symbol_placement','',false);
                if ($currency_symbol_placement === 'before'){ $before = $currency_symbol;}else $before = '';
                if ($currency_symbol_placement === 'after'){ $after = $currency_symbol;}else $after = '';
                if ($currency_symbol_placement === 'afterspace'){ $afterspace = " ".$currency_symbol;}else $afterspace = "";
            ?>

            <div class="row">
                <div class="col-xs-12 col-md-6 col-lg-5"></div>
                <div class="col-lg-2"></div>
                <div class="col-xs-12 col-md-6 col-lg-5 text-right">


                    <table class="table table-condensed">
                        <tbody>
                        <tr>
                            <td><?php echo Utilities::trans('invoice_date'); ?></td>
                            <td style="text-align:right;">
                                <?php $dateHelper = new DateHelper();    
                                      echo $dateHelper->date_from_mysql($model->invoice_date_created); 
                                ?>
                            </td>
                        </tr>
                        <tr class="<?php echo($is_overdue ? 'overdue' : '') ?>">
                            <td><?php echo Utilities::trans('due_date'); ?></td>
                            <td class="text-right">
                                <?php if (!empty($model->invoice_date_due)) {
                                        $invoicedatedue = Yii::$app->formatter->asDate($model->invoice_date_due,'php:d mm Y');
                                        echo $invoicedatedue;
                                    }else
                                    {
                                        echo Utilities::trans('due_date');
                                    }
                                ?>
                            </td>
                        </tr>
                        <tr class="<?php echo($is_overdue ? 'overdue' : '') ?>">
                            <td><?php echo Utilities::trans('amount_due'); ?></td>
                            <td style="text-align:right;">
                                <?php if (!empty($model->salesinvoiceamount->invoice_balance)) {
                                        echo $before.$model->salesinvoiceamount->invoice_balance.$after.$afterspace;
                                    }else
                                    {
                                        echo Utilities::trans('amount_due');
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php if ($payment_method): ?>
                            <tr>
                                <td><?php echo Utilities::trans('payment_method') . ': '; ?></td>
                                <td><?php
                                        echo $model->paymentmethod->payment_method_name;
                                     ?>     
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <br>

            <div class="invoice-items">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <?php
                                $details = $model->salesorderdetails;
                                echo '<thead><tr><th>Description</th><th class="text-right">Unit Price</th><th class="text-right">Order Quantity</th><th class="text-right">Subtotal</th></tr></thead>';
                                $sum_unit_price = 0;
                                $sum_total = 0;
                                foreach ( $details as $key => $value)
                                 {
                                     $getdate_id = $details[$key]['sales_order_id']; 
                                     $getdate = Salesorderheader::find()->where(['sales_order_id'=>$getdate_id])->one();
                                     echo '<tr class="bottom-border">';
                                     $cleandate = Yii::$app->formatter->asDate($getdate['clean_date'],'php:d-m-Y');
                                     echo '<td>Window Cleaning on '.$cleandate.'</td>';
                                     echo '<td class="amount text-right">'.$before.$details[$key]['unit_price'].$after.$afterspace.'</td>';
                                     echo '<td class="amount text-right">'.$details[$key]['order_qty'].$afterspace.'</td>';
                                     echo '<td class="amount text-right">'.$before.$details[$key]['unit_price'].$after.$afterspace.'</td>';
                                     echo '</tr>';
                                     $sum_unit_price += $details[$key]['unit_price'];
                                     $sum_total +=  $details[$key]['paid'];      
                                 } 
                                 $invoice_balance = $sum_unit_price - $sum_total;
                            ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($balance->invoice_balance == 0) { ?>
                    <img src="<?= Yii::$app->request->baseUrl.Utilities::getAssetholderRelativeUrl().'core/img/paid.png';?>" class="paid-stamp">
                <?php } ?>
                <?php if ($is_overdue) { ?>
                    <img src="<?= Yii::$app->request->baseUrl.Utilities::getAssetholderRelativeUrl().'core/img/overdue.png';?>" class="overdue-stamp">
                <?php } ?>

            </div><!-- .invoice-items -->

            <hr>

            <div class="row">

                <?php if ($model->invoice_terms) { ?>
                    <div class="col-xs-12 col-md-6">
                        <h4><?php echo Utilities::trans('terms'); ?></h4>
                        <p><?php echo nl2br(Html::encode($model->invoice_terms)); ?></p>
                    </div>
                <?php } ?>

                <?php
                if (count($attachments) > 0) { ?>
                    <div class="col-xs-12 col-md-6">
                        <h4><?php echo Utilities::trans('attachments'); ?></h4>
                        <div class="table-responsive">
                            <table class="table table-condensed">
                                <?php foreach ($attachments as $attachment) { ?>
                                    <tr class="attachments">
                                        <td><?php echo $attachment['name']; ?></td>
                                        <td>
                                            <a href="<?= Url::toRoute(['@web/invoice/get/attachment/' . $attachment['fullname']]); ?>"
                                               class="btn btn-primary btn-sm">
                                                <i class="fa fa-download"></i> <?php echo Utilities::trans('download') ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div><!-- .invoice-items -->
    </div><!-- #content -->
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>