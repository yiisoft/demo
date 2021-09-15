<?php

declare(strict_types=1);

use App\Invoice\Asset\CompactAsset;
use App\Asset\AppAsset;
use App\Widget\PerformanceMetrics;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Html\Html;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Yii\Bootstrap5\Nav;
use Yiisoft\Yii\Bootstrap5\NavBar;
use Yiisoft\Yii\Bootstrap5\Breadcrumbs;

/**
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Assets\AssetManager $assetManager
 * @var string $content
 *
 * @see \App\ApplicationViewInjection
 * @var \App\User\User $user 
 * @var string $csrf;
 * @var string $brandLabel
 */

$assetManager->register([
    CompactAsset::class,
    AppAsset::class
]);

$this->addCssFiles($assetManager->getCssFiles());
$this->addCssStrings($assetManager->getCssStrings());
$this->addJsFiles($assetManager->getJsFiles());
$this->addJsStrings($assetManager->getJsStrings());
$this->addJsVars($assetManager->getJsVars());

$currentRouteName = $currentRoute->getRoute() === null ? '' : $currentRoute->getRoute()->getName();
$this->beginPage();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yii Demo<?= $this->getTitle() ? ' - ' . $this->getTitle() : '' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">    
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
            $user->getId() === null
                ? [
                ['label' => $translator->translate('menu.blog'), 'url' => $urlGenerator->generate('blog/index'), 'active' => StringHelper::startsWith($currentRouteName, 'blog/') && $currentRouteName !== 'blog/comment/index'],
                ['label' => $translator->translate('menu.comments_feed'), 'url' => $urlGenerator->generate('blog/comment/index')],
                ['label' => $translator->translate('menu.users'), 'url' => $urlGenerator->generate('user/index'), 'active' => StringHelper::startsWith($currentRouteName, 'user/')],
                ['label' => $translator->translate('menu.contact'), 'url' => $urlGenerator->generate('site/contact')],
                ['label' => $translator->translate('menu.swagger'), 'url' => $urlGenerator->generate('swagger/index')],                
            ] :
            [
                ['label' => $translator->translate('invoice.home'), 'url' => $urlGenerator->generate('invoice/index'),'active' => StringHelper::startsWith($currentRouteName, 'invoice/') && $currentRouteName !== 'invoice/index',
                    'items' => [
                                ['label' => $translator->translate('invoice.generator'),'url'=>$urlGenerator->generate('generator/index')],
                                ['label' => $translator->translate('invoice.generators.relation'),'url'=>$urlGenerator->generate('generatorrelation/index')],  
                                ['label' => $translator->translate('invoice.setting'),'url'=>$urlGenerator->generate('setting/index')],
                                ['label' => $translator->translate('invoice.client'),'url'=>$urlGenerator->generate('client/index')],
                                ['label' => $translator->translate('invoice.client.custom'),'url'=>$urlGenerator->generate('clientcustom/index')],
                                ['label' => $translator->translate('invoice.client.note'),'url'=>$urlGenerator->generate('clientnote/index')],
                                ['label' => $translator->translate('invoice.email.template'),'url'=>$urlGenerator->generate('emailtemplate/index')],
                                ['label' => $translator->translate('invoice.family'),'url'=>$urlGenerator->generate('family/index')],
                                ['label' => $translator->translate('invoice.tax.rate'),'url'=>$urlGenerator->generate('taxrate/index')],
                                ['label' => $translator->translate('invoice.unit'),'url'=>$urlGenerator->generate('unit/index')],
                                ['label' => $translator->translate('invoice.product'),'url'=>$urlGenerator->generate('product/index')],
                                ['label' => $translator->translate('invoice.project'),'url'=>$urlGenerator->generate('project/index')],
                                ['label' => $translator->translate('invoice.task'),'url'=>$urlGenerator->generate('task/index')],
                                ['label' => $translator->translate('invoice.group'),'url'=>$urlGenerator->generate('group/index')],
                                ['label' => $translator->translate('invoice.invoice'),'url'=>$urlGenerator->generate('inv/index')],
                                ['label' => $translator->translate('invoice.invoice.item'),'url'=>$urlGenerator->generate('invitem/index')],
                                ['label' => $translator->translate('invoice.invoice.amount'),'url'=>$urlGenerator->generate('invamount/index')],
                                ['label' => $translator->translate('invoice.invoice.tax.rate'),'url'=>$urlGenerator->generate('invtaxrate/index')],
                                ['label' => $translator->translate('invoice.invoice.recurring'),'url'=>$urlGenerator->generate('recurring/index')],
                                ['label' => $translator->translate('invoice.invoice.item.lookup'),'url'=>$urlGenerator->generate('itemlookup/index')],
                                ['label' => $translator->translate('invoice.quote'),'url'=>$urlGenerator->generate('quote/index')],
                                ['label' => $translator->translate('invoice.quote.item'),'url'=>$urlGenerator->generate('quoteitem/index')],
                                ['label' => $translator->translate('invoice.quote.amount'),'url'=>$urlGenerator->generate('quoteamount/index')],
                                ['label' => $translator->translate('invoice.quote.item.amount'),'url'=>$urlGenerator->generate('quoteitemamount/index')],
                                ['label' => $translator->translate('invoice.quote.tax.rate'),'url'=>$urlGenerator->generate('quotetaxrate/index')],
                                ['label' => $translator->translate('invoice.sumex'),'url'=>$urlGenerator->generate('sumex/index')],
                                ['label' => $translator->translate('invoice.merchant'),'url'=>$urlGenerator->generate('merchant/index')],
                                ['label' => $translator->translate('invoice.invoice.custom'),'url'=>$urlGenerator->generate('invcust/index')], 
                                ['label' => $translator->translate('invoice.custom.field'),'url'=>$urlGenerator->generate('customfield/index')],
                                ['label' => $translator->translate('invoice.custom.value'),'url'=>$urlGenerator->generate('customvalue/index')],
                                ['label' => $translator->translate('invoice.payment'),'url'=>$urlGenerator->generate('payment/index')],
                                ['label' => $translator->translate('invoice.payment.method'),'url'=>$urlGenerator->generate('paymentmethod/index')],
                                ['label' => $translator->translate('invoice.payment.custom'),'url'=>$urlGenerator->generate('paymentcustom/index')],
                               ]
                ],
                ['label' => $translator->translate('invoice.client'), 
                     'items' => [
                                ['label' => $translator->translate('invoice.add'),'url'=>$urlGenerator->generate('client/add')],
                                ['label' => $translator->translate('invoice.view'),'url'=>$urlGenerator->generate('client/index')],
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
                                ['label' => $translator->translate('invoice.recurring'),'url'=>$urlGenerator->generate('recurring/index')], 
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
                    ['label' => 'Setting', 
                     'items' => [
                                 ['label' => $translator->translate('invoice.view'),'url'=>$urlGenerator->generate('setting/index')],
                               ],
                    ],
                
            ]       
        );

echo Nav::widget()
        ->currentPath($currentRoute->getUri()->getPath())
        ->options(['class' => 'navbar-nav'])
        ->items(
            $user->getId() === null
                ? [
                 ['label' => $translator->translate('menu.login'), 'url' => $urlGenerator->generate('site/login')],
                 ['label' => $translator->translate('menu.signup'), 'url' => $urlGenerator->generate('site/signup')],
            ]
                : [Form::widget()
                    ->action($urlGenerator->generate('site/logout'))
                    ->options(['csrf' => $csrf])
                    ->begin()
                    . Html::submitButton($translator->translate('menu.logout ({login})', ['login' => Html::encode($user->getLogin())]), ['class' => 'dropdown-item'])
                    . Form::end()],
        );
echo NavBar::end();

?><main class="container py-4">
<?php if ($user->getId() <> null) {
    echo Breadcrumbs::widget()->links([
                                   ['label' => $translator->translate('invoice.generator.add'),'url'=>$urlGenerator->generate('generator/add')],
                                   ['label' => $translator->translate('invoice.generator.relations.add'),'url'=>$urlGenerator->generate('generatorrelation/add')],
                                   ['label' => $translator->translate('invoice.setting.add'),'url'=>$urlGenerator->generate('setting/add')],
                                   ['label' => $translator->translate('invoice.client.add'),'url'=>$urlGenerator->generate('client/add')],
                                   ['label' => $translator->translate('invoice.client.custom.add'),'url'=>$urlGenerator->generate('clientcustom/add')],
                                   ['label' => $translator->translate('invoice.client.note.add'),'url'=>$urlGenerator->generate('clientnote/add')], 
                                   ['label' => $translator->translate('invoice.email.template.add'),'url'=>$urlGenerator->generate('emailtemplate/add')],
                                   ['label' => $translator->translate('invoice.family.add'),'url'=>$urlGenerator->generate('family/add')],
                                   ['label' => $translator->translate('invoice.tax.rate.add'),'url'=>$urlGenerator->generate('taxrate/add')],
                                   ['label' => $translator->translate('invoice.unit.add'),'url'=>$urlGenerator->generate('unit/add')],
                                   ['label' => $translator->translate('invoice.product.add'),'url'=>$urlGenerator->generate('product/add')],
                                   ['label' => $translator->translate('invoice.project.add'),'url'=>$urlGenerator->generate('project/add')],
                                   ['label' => $translator->translate('invoice.task.add'),'url'=>$urlGenerator->generate('task/add')],
                                   ['label' => $translator->translate('invoice.group.add'),'url'=>$urlGenerator->generate('group/add')],
                                   ['label' => $translator->translate('invoice.invoice.add'),'url'=>$urlGenerator->generate('inv/add')],
                                   ['label' => $translator->translate('invoice.invoice.item.add'),'url'=>$urlGenerator->generate('invitem/add')],
                                   ['label' => $translator->translate('invoice.invoice.amount.add'),'url'=>$urlGenerator->generate('invamount/add')],
                                   ['label' => $translator->translate('invoice.invoice.tax.rate.add'),'url'=>$urlGenerator->generate('invtaxrate/add')],
                                   ['label' => $translator->translate('invoice.item.lookup'),'url'=>$urlGenerator->generate('itemlookup/index')],
                                   ['label' => $translator->translate('invoice.quote.add'),'url'=>$urlGenerator->generate('quote/add')],
                                   ['label' => $translator->translate('invoice.quote.item.add'),'url'=>$urlGenerator->generate('quoteitem/add')],
                                   ['label' => $translator->translate('invoice.quote.item.amount'),'url'=>$urlGenerator->generate('quoteitemamount/add')],
                                   ['label' => $translator->translate('invoice.quote.amount.add'),'url'=>$urlGenerator->generate('quoteamount/add')],
                                   ['label' => $translator->translate('invoice.quote.tax.rate.add'),'url'=>$urlGenerator->generate('quotetaxrate/add')],
                                   ['label' => $translator->translate('invoice.sumex.add'),'url'=>$urlGenerator->generate('sumex/add')],
                                   ['label' => $translator->translate('invoice.merchant.add'),'url'=>$urlGenerator->generate('merchant/add')],
                                   ['label' => $translator->translate('invoice.custom.invoice.add'),'url'=>$urlGenerator->generate('invcust/add')],
                                   ['label' => $translator->translate('invoice.custom.field.add'),'url'=>$urlGenerator->generate('customfield/add')],
                                   ['label' => $translator->translate('invoice.custom.value.add'),'url'=>$urlGenerator->generate('customvalue/add')],
                                   ['label' => $translator->translate('invoice.invoice.recurring.add'),'url'=>$urlGenerator->generate('recurring/add')],
                                   ['label' => $translator->translate('invoice.payment.method.add'),'url'=>$urlGenerator->generate('paymentmethod/add')],
                                   ['label' => $translator->translate('invoice.payment.custom.add'),'url'=>$urlGenerator->generate('paymentcustom/add')],
                                   ['label' => $translator->translate('invoice.payment.add'),'url'=>$urlGenerator->generate('payment/add')],
                                  ])
                          ->activeItemTemplate("<li class=\"breadcrumb-item active\" aria-current=\"page\">{link}</li>\n")
                          ->homelink(['label' => $translator->translate('invoice.home'),'url'=>$urlGenerator->generate('invoice/index')]);
    echo $content;
}
?></main>

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
