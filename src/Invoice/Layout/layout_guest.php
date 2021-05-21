<?php
   use frontend\assets\AppAsset;
   use frontend\modules\invoice\assets\CoreCustomCssJsAsset;
   use frontend\modules\invoice\assets\InvoiceThemeNoMonospaceAsset;
   use frontend\modules\invoice\assets\InvoiceThemeMonospaceAsset;   
   use frontend\modules\invoice\application\components\Utilities;
   use yii\helpers\Html;
   use yii\helpers\Url;
   use Yii;
   use yii\bootstrap4\NavBar;
   use frontend\widgets\Alert;
   AppAsset::register($this);
   CoreCustomCssJsAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html class="no-js" lang="<?= Utilities::trans('cldr'); ?>">
<head>
    <title>
        <?php
        if ($this->context->mdl_settings->setting('custom_title') != '') {
            echo $this->context->mdl_settings->setting('custom_title', '', true);
        } else {
            echo Html::encode('Invoice');
        } ?>
    </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="NOINDEX,NOFOLLOW">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css">
    <?php if ($this->context->mdl_settings->get_setting('monospace_amounts') == 1) { 
            //use the invoice theme with monospace.css
               InvoiceThemeMonospaceAsset::register($this);
          }
          else {            
               InvoiceThemeNoMonospaceAsset::register($this);  
          }
    ?>
    <?php
       $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => '/favicon.png']); 
       Html::csrfMetaTags(); 
       $this->head();
    ?>
</head>
<body class="<?php echo $this->context->mdl_settings->setting('disable_sidebar') ? 'hidden-sidebar' : ''; ?>">
<?php $this->beginBody()?>
<div class="wrap">

<?php if (Yii::$app->user->can('Make payment online')) { ;?> 
<?php NavBar::begin(['options' => ['class' => 'navbar navbar-inverse navbar-expand-sm', 'role'=>'navigation','id'=>'ip-navbar-collapse']]);?>
    <div>
        <div class="navbar-header">
            <button type="button" class="navbar-toggle"
                    data-toggle="collapse" data-target="#ip-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <?= Utilities::trans('menu') ?> &nbsp; <i class="fa fa-bars"></i>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="ip-navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="<?php echo Url::toRoute(['/invoice/guest']); ?>">
                        <span class="hidden-md"><?= Utilities::trans('dashboard');?></span>
                        <span class="visible-md-inline-block"><?= Utilities::trans('dashboard');?></span>
                    </a> 
                </li>
                <li><a href="<?php echo Url::toRoute(['/invoice/invoices/open']); ?>">
                        <span class="hidden-md"><?= Utilities::trans('invoices');?></span>
                        <span class="visible-md-inline-block"><?= Utilities::trans('invoices');?></span>
                    </a> 
                </li>
                <li><a href="<?php echo Url::toRoute(['/invoice/payments']); ?>">
                        <span class="hidden-md"><?= Utilities::trans('payments');?></span>
                        <span class="visible-md-inline-block"><?= Utilities::trans('payments');?></span>
                    </a> 
                </li>                
            </ul>
            <ul class="nav navbar-nav navbar-right settings">
                <li>
                    <a href="<?php echo Url::toRoute(['/site/index']); ?>"
                       class="tip icon logout" data-placement="bottom"
                       title="<?= Utilities::trans('menu'); ?>">
                        <i class="fa fa-home"></i>
                        <span class="visible-xs">&nbsp;<?= Utilities::trans('menu'); ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
<?php NavBar::end(); ?>
<?php } ?>   
<?php if (Yii::$app->user->can('Manage Admin')) { ;?> 
<?php NavBar::begin(['options' => ['class' => 'navbar navbar-inverse navbar-expand-sm', 'role'=>'navigation','id'=>'ip-navbar-collapse']]);?>
    <div>
        <div class="navbar-header">
            <button type="button" class="navbar-toggle"
                    data-toggle="collapse" data-target="#ip-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <?= Utilities::trans('menu') ?> &nbsp; <i class="fa fa-bars"></i>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="ip-navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="<?php echo Url::toRoute(['/invoice/guest']); ?>">
                        <span class="hidden-md"><?= Utilities::trans('dashboard');?></span>
                        <span class="visible-md-inline-block"><?= Utilities::trans('dashboard');?></span>
                    </a> 
                </li>
                <li><a href="<?php echo Url::toRoute(['/invoice/invoices/open']); ?>">
                        <span class="hidden-md"><?= Utilities::trans('invoices');?></span>
                        <span class="visible-md-inline-block"><?= Utilities::trans('invoices');?></span>
                    </a> 
                </li>
                <li><a href="<?php echo Url::toRoute(['/invoice/payments']); ?>">
                        <span class="hidden-md"><?= Utilities::trans('payments');?></span>
                        <span class="visible-md-inline-block"><?= Utilities::trans('payments');?></span>
                    </a> 
                </li>                
            </ul>
            <ul class="nav navbar-nav navbar-right settings">
                <li>
                    <a href="<?php echo Url::toRoute(['/site/index']); ?>"
                       class="tip icon logout" data-placement="bottom"
                       title="<?= Utilities::trans('menu'); ?>">
                        <i class="fa fa-home"></i>
                        <span class="visible-xs">&nbsp;<?= Utilities::trans('menu'); ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
<?php NavBar::end(); ?>
<?php } ?>    
<div id="main-area">
    <div class="sidebar hidden-xs <?php if ($this->context->mdl_settings->get_setting('disable_sidebar') == 1) {
        echo 'hidden';
    } ?>">
        <ul>
            <li>
                <a href="<?= Url::to(['/invoice/guest']); ?>" title="<?= Utilities::trans('dashboard'); ?>" class="tip"
                   data-placement="right">
                    <i class="fa fa-dashboard"></i>
                </a>
            </li>
            <li>
                <a href="<?= Url::to(['/invoice/invoices/open']); ?>" title="<?= Utilities::trans('invoices'); ?>"
                   class="tip" data-placement="right">
                    <i class="fa fa-file-text"></i>
                </a>
            </li>
            <li>
                <a href="<?= Url::to(['/invoice/payments']); ?>" title="<?= Utilities::trans('payments'); ?>"
                   class="tip" data-placement="right">
                    <i class="fa fa-money"></i>
                </a>
            </li>
        </ul>
    </div>
    <div id="main-content">
        <?= Alert::widget() ?>
        <?= $content; ?>
    </div>
</div>
<div id="modal-placeholder"></div>

<?php echo Yii::$app->controller->renderPartial('/layouts/includes/fullpage-loader',['echohelper'=>$this->context->echoHelper]); ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
</div>