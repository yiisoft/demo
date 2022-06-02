<?php
declare(strict_types=1);

use App\Invoice\Asset\InvoiceAsset;
use App\Asset\AppAsset;
use App\Widget\PerformanceMetrics;
use Yiisoft\Html\Tag\Button;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Html\Html;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Yii\Bootstrap5\Nav;
use Yiisoft\Yii\Bootstrap5\NavBar;

/**
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Assets\AssetManager $assetManager
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var string $content
 * @see \App\ApplicationViewInjection
 * @var \App\User\User|null $user
 * @var string $csrf
 * @var string $brandLabel
 */

$assetManager->register(AppAsset::class);
$assetManager->register(InvoiceAsset::class);
$assetManager->register(Yiisoft\Yii\Bootstrap5\Assets\BootstrapAsset::class);

$this->addCssFiles($assetManager->getCssFiles());
$this->addCssStrings($assetManager->getCssStrings());
$this->addJsFiles($assetManager->getJsFiles());
$this->addJsStrings($assetManager->getJsStrings());
$this->addJsVars($assetManager->getJsVars());

$currentRouteName = $currentRoute->getName() ?? '';

$isGuest = $user === null || $user->getId() === null;

$xdebug = extension_loaded('xdebug') ? 'php.ini zend_extension Installed: Performance compromised!' : 'php.ini zend_extension Commented out: Performance NOT compromised';

// Platform, Performance, and Clear Assets Cache, and links Menu will disappear if set to false;
$debug_mode = true;

$this->beginPage();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yii Demo<?= $this->getTitle() ? ' - ' . $this->getTitle() : '' ?></title> 
    <?php $this->head() ?>
</head>
<body>
<?php
$this->beginBody();

echo NavBar::widget()
      ->brandText($brandLabel)
      ->brandUrl($urlGenerator->generate('site/index'))
      ->options(['class' => 'navbar navbar-light bg-light navbar-expand-sm text-white'])
      ->begin();
echo Nav::widget()
        ->currentPath($currentRoute->getUri()->getPath())
        ->options(['class' => 'navbar-nav mx-auto', 'style'=>'background-color: #e3f2fd;'])
        ->items( 
            $isGuest
                ? [
                ['label' => $translator->translate('menu.blog'), 'url' => $urlGenerator->generate('blog/index'), 'active' => StringHelper::startsWith($currentRouteName, 'blog/') && $currentRouteName !== 'blog/comment/index'],
                ['label' => $translator->translate('menu.comments_feed'), 'url' => $urlGenerator->generate('blog/comment/index')],
                ['label' => $translator->translate('menu.users'), 'url' => $urlGenerator->generate('user/index'), 'active' => StringHelper::startsWith($currentRouteName, 'user/')],
                ['label' => $translator->translate('menu.contact'), 'url' => $urlGenerator->generate('site/contact')],
                ['label' => $translator->translate('menu.swagger'), 'url' => $urlGenerator->generate('swagger/index')],                
            ] :
            [               
                ['label' => $translator->translate('invoice.client'), 
                     'items' => [
                                ['label' => $translator->translate('invoice.add'),'url'=>$urlGenerator->generate('client/add')],
                                ['label' => $translator->translate('invoice.view'),'url'=>$urlGenerator->generate('client/index')],                                
                                ['label' => $translator->translate('invoice.client.note.add'),'url'=>$urlGenerator->generate('clientnote/add')],
                               ],
                    ],
                    ['label' => $translator->translate('invoice.quote'), 
                     'items' => [
                                ['label' => $translator->translate('invoice.add'),'url'=>$urlGenerator->generate('quote/add')],
                                ['label' => $translator->translate('invoice.view'),'url'=>$urlGenerator->generate('quote/index')],
                               ],
                    ],
                    ['label' => $translator->translate('invoice.invoice'), 
                     'items' => [
                                ['label' => $translator->translate('invoice.add'),'url'=>$urlGenerator->generate('inv/add')],
                                ['label' => $translator->translate('invoice.view'),'url'=>$urlGenerator->generate('inv/index')],
                                ['label' => $translator->translate('invoice.recurring'),'url'=>$urlGenerator->generate('invrecurring/index')], 
                               ],
                    ],
                    ['label' => $translator->translate('invoice.payment'), 
                     'items' => [
                                ['label' => $translator->translate('invoice.enter'),'url'=>$urlGenerator->generate('payment/add')],
                                ['label' => $translator->translate('invoice.view'),'url'=>$urlGenerator->generate('payment/index')],
                                ['label' => $translator->translate('invoice.online.log'),'url'=>'#'] 
                               ],
                    ],
                    ['label' => $translator->translate('invoice.product'), 
                     'items' => [
                                ['label' => $translator->translate('invoice.create'),'url'=>$urlGenerator->generate('product/add')],
                                ['label' => $translator->translate('invoice.view'),'url'=>$urlGenerator->generate('product/index')],
                                ['label' => $translator->translate('invoice.family'),'url'=>$urlGenerator->generate('family/index')],
                                ['label' => $translator->translate('invoice.unit'),'url'=>$urlGenerator->generate('unit/index')],
                               ],
                    ],
                    ['label' => $translator->translate('invoice.task'), 
                     'items' => [
                                ['label' => $translator->translate('invoice.create'),'url'=>$urlGenerator->generate('task/add')],
                                ['label' => $translator->translate('invoice.view'),'url'=>$urlGenerator->generate('task/index')],
                               ],
                    ],
                    ['label' => $translator->translate('invoice.project'), 
                     'items' => [
                                ['label' => $translator->translate('invoice.create'),'url'=>$urlGenerator->generate('project/add')],
                                ['label' => $translator->translate('invoice.view'),'url'=>$urlGenerator->generate('project/index')],
                               ],
                    ],
                    ['label' => $translator->translate('invoice.report'), 
                     'items' => [
                                ['label' => $translator->translate('invoice.create'),'url'=>'#'],
                                ['label' => $translator->translate('invoice.view'),'url'=>'#'],
                               ],
                    ],
                    ['label' => $translator->translate('invoice.setting'), 
                     'items' => [['label' => $translator->translate('invoice.view'),'options'=>['style'=>'background-color: #ffcccb'],'url'=>$urlGenerator->generate('setting/debug_index'),'visible'=>$debug_mode],
                                 ['label' => $translator->translate('invoice.setting.add'),'options'=>['style'=>'background-color: #ffcccb'], 'url'=>$urlGenerator->generate('setting/add'),'visible'=>$debug_mode],    
                                 ['label' => $translator->translate('invoice.view'),'url'=>$urlGenerator->generate('setting/tab_index')],                         
                                 ['label' => $translator->translate('invoice.email.template'),'url'=>$urlGenerator->generate('emailtemplate/index')],
                                 ['label' => $translator->translate('invoice.custom.field'),'url'=>$urlGenerator->generate('customfield/index')],
                                 ['label' => $translator->translate('invoice.group'),'url'=>$urlGenerator->generate('group/index')],
                                 ['label' => $translator->translate('invoice.archive'),'url'=>$urlGenerator->generate('inv/archive')],
                                 ['label' => $translator->translate('invoice.payment.method'),'url'=>$urlGenerator->generate('paymentmethod/index')],   
                                 ['label' => $translator->translate('invoice.tax.rate'),'url'=>$urlGenerator->generate('taxrate/index')],
                                 ['label' => $translator->translate('invoice.user.account'),'url'=>$urlGenerator->generate('userinv/index')],
                                 ['label' => $translator->translate('invoice.setting.company'),'url'=>$urlGenerator->generate('company/index')],
                                 ['label' => $translator->translate('invoice.setting.company.private'),'url'=>$urlGenerator->generate('companyprivate/index')],
                                 ['label' => $translator->translate('invoice.setting.company.profile'),'url'=>$urlGenerator->generate('profile/index')],
                               ],
                    ],
                    ['label' => $translator->translate('invoice.platform'), 'options'=>['style'=>'background-color: #ffcccb'],'visible'=>$debug_mode,
                     'items' => [
                                 ['label' => $translator->translate('invoice.platform.editor'). ': Netbeans 12.4 64 bit'], 
                                 ['label' => $translator->translate('invoice.platform.server'). ': Wampserver 3.2.8 64 bit'],
                                 ['label' => $translator->translate('invoice.platform.sqlPath'). ': src\Invoice\Sql\settings.sql'],
                                 ['label' => $translator->translate('invoice.platform.mySqlVersion'). ': 5.7.31 || 8.0.29 '],
                                 ['label' => $translator->translate('invoice.platform.PhpVersion'). ': 8.1.6 (Compatable with PhpAdmin 5.1.3)'],
                                 ['label' => $translator->translate('invoice.platform.PhpMyAdmin'). ': 5.1.3 (Compatable with php 8.1.6)'],
                                 ['label' => $translator->translate('invoice.platform.PhpSupport'), 'url'=>'https://php.net/supported-versions'],
                                 ['label' => $translator->translate('invoice.platform.update'), 'url'=>'https://wampserver.aviatechno.net/'], 
                                 ['label' => $translator->translate('invoice.vendor.nikic.fast-route'), 'url'=>'https://github.com/nikic/FastRoute'],
                                 ['label' => $translator->translate('invoice.platform.netbeans.UTF-8'), 'url'=>'https://stackoverflow.com/questions/59800221/gradle-netbeans-howto-set-encoding-to-utf-8-in-editor-and-compiler'],
                               ],
                    ],
                     ['label' => $translator->translate('invoice.performance'),  'options'=>['style'=>'background-color: #ffcccb'],'visible'=>$debug_mode,
                     'items' => [
                                 ['label' => $translator->translate('invoice.platform.xdebug'). ' '.$xdebug],  
                                 ['label' => 'php.ini: opcache.memory_consumption=128'],
                                 ['label' => 'php.ini: oopcache.interned_strings_buffer=8'],
                                 ['label' => 'php.ini: opcache.max_accelerated_files=4000'],
                                 ['label' => 'php.ini: opcache.revalidate_freq=60'],
                                 ['label' => 'php.ini: opcache.enable=1'],
                                 ['label' => 'php.ini: opcache.enable_cli=1'],
                                 ['label' => 'config.params: yiisoft/yii-debug: enabled , disable for improved performance'], 
                                 ['label' => 'config.params: yiisoft/yii-debug-api: enabled, disable for improved performance'],
                               ],
                    ],
                     ['label' => $translator->translate('invoice.generator'),  'options'=>['style'=>'background-color: #ffcccb'],'visible'=>$debug_mode,
                     'items' => [
                                   ['label' => $translator->translate('invoice.generator'),'url'=>$urlGenerator->generate('generator/index')],
                                   ['label' => $translator->translate('invoice.generator.add'),'url'=>$urlGenerator->generate('generator/add')],
                                   ['label' => $translator->translate('invoice.generator.relations.add'),'url'=>$urlGenerator->generate('generatorrelation/add')],                                                                    
                                   ['label' => $translator->translate('invoice.development.progress'),'url'=>$urlGenerator->generate('invoice/index')]
                               ],
                    ],
                    ['label' => $translator->translate('invoice.utility.assets.clear'),
                     'url'=>$urlGenerator->generate('setting/clear'),'options'=>['data-toggle'=>'tooltip', 
                     'title'=>'Clear the assets cache which resides in /public/assets.','style'=>'background-color: #ffcccb'],
                     'visible'=>$debug_mode],
                    ['label' => $translator->translate('invoice.debug'),
                     'url'=>'',
                     'options'=>['data-toggle'=>'tooltip', 'title'=>'Disable in Invoice/Layout/main.php. Red background links and menus will disappear.','style'=>'background-color: #ffcccb'],
                     'visible'=>$debug_mode],
                    ['label' => $translator->translate('menu.blog'),
                     'url'=>$urlGenerator->generate('blog/index'),'options'=>['data-toggle'=>'tooltip', 'title'=>'Change the locale here.'] 
                     ],
            ]       
        );

echo Nav::widget()
                ->currentPath($currentRoute->getUri()->getPath())
                ->options(['class' => 'navbar-nav'])
                ->items(
                    [
                        [
                            'label' => $translator->translate('menu.login'),
                            'url' => $urlGenerator->generate('auth/login'),
                            'visible' => $isGuest,
                        ],
                        [
                            'label' => $translator->translate('menu.signup'),
                            'url' => $urlGenerator->generate('auth/signup'),
                            'visible' => $isGuest,
                        ],
                       $isGuest ? '' : Form::tag()
                                ->post($urlGenerator->generate('auth/logout'))
                                ->csrf($csrf)
                                ->open()
                            . '<div class="mb-1">'
                            . Button::submit(
                                $translator->translate('menu.logout', ['login' => Html::encode($user->getLogin())])
                            )
                                ->class('btn btn-primary')
                            . '</div>'
                            . Form::tag()->close()
                    ],
                );

echo NavBar::end();?>
<main class="container py-4">
    <?php 
       echo $content;
    ?>
</main>
<footer class="container py-4">
    <?= PerformanceMetrics::widget() ?>    
</footer>
    <?php
        $this->endBody();
    ?>
</body>
</html>
<?php
$this->endPage(true);
