<?php
   declare(strict_types=1);
   
   use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;
   use Yiisoft\Yii\Bootstrap5\Alert;
   use App\Invoice\Asset\InvoiceThemeNoMonospaceAsset;
   use App\Invoice\Asset\CoreCustomCssJsAsset;   
   use App\Invoice\Components\Utilities;
   use App\Invoice\Components\Mdl_settings;
   use App\Invoice\Helpers\EchoHelper;
   use App\Invoice\Helpers\DateHelper;
   use App\Invoice\Libraries\Lang;
   use App\Invoice\Setting\SettingRepository;   
   use App\Asset\AppAsset;  
    /**
    * @var \Yiisoft\Assets\AssetManager $assetManager
    * @var string $content    
    */

   $assetManager->register([
         AppAsset::class,
         InvoiceThemeNoMonospaceAsset::class,
         CoreCustomCssJsAsset::class        
   ]);
   
   $this->setCssFiles($assetManager->getCssFiles());
   $this->setJsFiles($assetManager->getJsFiles());
   $this->setJsStrings($assetManager->getJsStrings());
   $this->setJsVars($assetManager->getJsVars());
   
?>
<?php $this->beginPage(); ?>  
<!DOCTYPE html>
<html class="no-js" lang="<?//= //$utilities->trans('cldr'); ?>">

<?php 
   $echohelper = new EchoHelper();
   $datehelper = new DateHelper();
   $mdl_settings = new Mdl_settings();
   $mdl_settings->load_settings();
   $language = $mdl_settings->get_setting('default_language');
   //load the current languages lines for the settings and the gateway for each individual folder
   $lang =[];
   $thislang=[];
   $lang = new Lang();
   $lang->load('ip', $language);
   $thislang = $lang->_language;
?>
<head>
<?php 
   ///$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => '/favicon.png']); 
   ///$this->registerCsrfMetaTags();    
   $this->head();
?> 
</head>
<body class="<?php echo $mdl_settings->get_setting('disable_sidebar') ? 'hidden-sidebar' : ''; ?>">
<?php $this->beginBody()?>
<div class="wrap">
    <noscript>
        <div class="alert alert-danger no-margin"><?php //$echohelper->_trans('please_enable_js'); ?></div>
    </noscript>
    <div>
    <?php echo  $this->viewRenderer->renderPartial('@src/Invoice/Layout/includes/navbar',['mdl_settings'=>$mdl_settings,'mylang'=>$thislang]);?> 
    </div>
    <div id="main-area">
        <?php
        // Display the sidebar if enabled
        if ($mdl_settings->get_setting('disable_sidebar') != 1) {
            echo $this->viewRenderer->renderPartial('@src/Invoice/Layout/includes/sidebar',['mdl_settings'=>$mdl_settings,'datehelper'=>$datehelper,'echohelper'=>$echohelper,'mylang'=>$thislang]);
        }
        else echo '';
        ?>
        <div id="main-content">
        <?php
            if (!empty($errors)) {
                foreach ($errors as $field => $error) {
                    echo Alert::widget()->options(['class' => 'alert-danger'])->body(Html::encode($field . ':' . $error));
                }
            }
        ?>    
        <?= $content; ?>
        </div>
    </div>
    <div id="modal-placeholder"></div>
    <?php echo $this->viewRenderer->renderPartial('@src/Invoice/Layout/includes/fullpage-loader',['echohelper'=>$echohelper]); ?>
    <?php $this->endBody() ?>
</div>
</body>
</html>
<?php $this->endPage() ?>
