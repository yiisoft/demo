<title><?php echo $mdl_settings->get_setting('custom_title', null, true) ?: 'InvoicePlane';?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="NOINDEX,NOFOLLOW">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"> 
<?php //$echohelper->_core_asset('css/custom.css'); ?>

<?php //if ($mdl_settings->get_setting('monospace_amounts') == 1) { ?>
    <?php //$echohelper->_theme_asset('css/monospace.css'); ?>
<?php //} ?>

<!--[if lt IE 9]>
<script src="<?php ///$echohelper->_core_asset('js/legacy.min.js'); ?>"></script>
<![endif]-->
<?php
   $js = <<< 'SCRIPT'
      Dropzone.autoDiscover = false;
      $(function () {
        $('.nav-tabs').tab();
        $('.tip').tooltip();

        $('body').on('focus', '.datepicker', function () {
            $(this).datepicker({
                autoclose: true,
                format: '<?php echo $datehelper->date_format_datepicker(); ?>',
                language: '<?php echo $echohelper->_trans("cldr"); ?>',
                weekStart: '<?php echo $mdl_settings->get_setting("first_day_of_week"); ?>',
                todayBtn: "linked"
            });
        });

        $(document).on('click', '.create-invoice', function () {
            $('#modal-placeholder').load("<?php Html::a('This',Url::toRoute('invoice/salesinvoicesetting/online')); ?>");
        });

        $(document).on('click', '.create-quote', function () {
            $('#modal-placeholder').load("<?php Html::a('This',Url::toRoute('invoice/salesinvoicesetting/online')); ?>");
        });

        $(document).on('click', '#btn_quote_to_invoice', function () {
            var quote_id = $(this).data('quote-id');
            $('#modal-placeholder').load("<?php Html::a('This',Url::toRoute('invoice/salesinvoicesetting/online'));?>/"
            + quote_id);
        });

        $(document).on('click', '#btn_copy_invoice', function () {
            var invoice_id = $(this).data('invoice-id');
            $('#modal-placeholder').load("<?phpHtml::a('This',Url::toRoute('invoice/salesinvoicesetting/online'));?>", {invoice_id: invoice_id});
        });

        $(document).on('click', '#btn_create_credit', function () {
            var invoice_id = $(this).data('invoice-id');
            $('#modal-placeholder').load("<?phpHtml::a('This',Url::toRoute('invoice/salesinvoicesetting/online')); ?>", {invoice_id: invoice_id});
        });

        $(document).on('click', '#btn_copy_quote', function () {
            var quote_id = $(this).data('quote-id');
            var client_id = $(this).data('client-id');
            $('#modal-placeholder').load("<?php Html::a('This',Url::toRoute('invoice/salesinvoicesetting/online')); ?>", {quote_id: quote_id,client_id: client_id});
        });

        $(document).on('click', '.client-create-invoice', function () {
            var client_id = $(this).data('client-id');
            $('#modal-placeholder').load("<?php Html::a('This',Url::toRoute('invoice/salesinvoicesetting/online')); ?>", {client_id: client_id});
        });

        $(document).on('click', '.client-create-quote', function () {
            var client_id = $(this).data('client-id');
            $('#modal-placeholder').load("<?php Html::a('This',Url::toRoute('invoice/salesinvoicesetting/online')); ?>", {client_id: client_id});
        });

        $(document).on('click', '.invoice-add-payment', function () {
            var invoice_id = $(this).data('invoice-id');
            var invoice_balance = $(this).data('invoice-balance');
            var invoice_payment_method = $(this).data('invoice-payment-method');
            var payment_cf_exist =  $(this).data('payment-cf-exist');
            $('#modal-placeholder').load("<?php Html::a('This',Url::toRoute('invoice/salesinvoicesetting/online')); ?>", {
                invoice_id: invoice_id,
                invoice_balance: invoice_balance,
                invoice_payment_method: invoice_payment_method,
                payment_cf_exist: payment_cf_exist
            });
        });

    });           
   SCRIPT;
   $this->registerJs($js);
   $js = <<< 'SCRIPT'
    $(function () { 
        $("[data-toggle='tooltip']").tooltip(); 
    });
    $(function () { 
        $("[data-toggle='popover']").popover(); 
    });
    SCRIPT;
    // Register tooltip/popover initialization javascript
    $this->registerJs($js);
?>
