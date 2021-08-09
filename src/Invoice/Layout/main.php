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
 * @var \Yiisoft\Router\UrlMatcherInterface $urlMatcher
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

$currentRoute = $urlMatcher->getCurrentRoute() === null ? '' : $urlMatcher->getCurrentRoute()->getName();
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
        ->currentPath($urlMatcher->getCurrentUri()->getPath())
        ->options(['class' => 'navbar-nav mx-auto'])
        ->items( 
            $user->getId() === null
                ? [
                ['label' => 'Blog', 'url' => $urlGenerator->generate('blog/index'), 'active' => StringHelper::startsWith($currentRoute, 'blog/') && $currentRoute !== 'blog/comment/index'],
                ['label' => 'Comments Feed', 'url' => $urlGenerator->generate('blog/comment/index')],
                ['label' => 'Users', 'url' => $urlGenerator->generate('user/index'), 'active' => StringHelper::startsWith($currentRoute, 'user/')],
                ['label' => 'Contact', 'url' => $urlGenerator->generate('site/contact')],
                ['label' => 'Swagger', 'url' => $urlGenerator->generate('swagger/index')],
                
            ] :
            [
                ['label' => 'Invoice', 'url' => $urlGenerator->generate('invoice/index'),'active' => StringHelper::startsWith($currentRoute, 'invoice/') && $currentRoute !== 'invoice/index',
                    'items' => [
                                ['label' =>'Generator','url'=>$urlGenerator->generate('generator/index')],
                                ['label' =>'Generator Relation','url'=>$urlGenerator->generate('generatorrelation/index')],  
                                ['label' =>'Setting','url'=>$urlGenerator->generate('setting/index')],
                                ['label' =>'Client','url'=>$urlGenerator->generate('client/index')],
                                ['label' =>'Client Custom','url'=>$urlGenerator->generate('clientcustom/index')],
                                ['label' =>'Client Note','url'=>$urlGenerator->generate('clientnote/index')],
                                ['label' =>'Email Template','url'=>$urlGenerator->generate('emailtemplate/index')],
                                ['label' =>'Family','url'=>$urlGenerator->generate('family/index')],
                                ['label' =>'Tax Rate','url'=>$urlGenerator->generate('taxrate/index')],
                                ['label' =>'Unit','url'=>$urlGenerator->generate('unit/index')],
                                ['label' =>'Product','url'=>$urlGenerator->generate('product/index')],
                                ['label' =>'Project','url'=>$urlGenerator->generate('project/index')],
                                ['label' =>'Task','url'=>$urlGenerator->generate('task/index')],
                                ['label' =>'Group','url'=>$urlGenerator->generate('group/index')],
                                ['label' =>'Invoice','url'=>$urlGenerator->generate('inv/index')],
                                ['label' =>'Invoice Item','url'=>$urlGenerator->generate('item/index')],
                                ['label' =>'Invoice Amount','url'=>$urlGenerator->generate('amount/index')],
                                ['label' =>'Sumex','url'=>$urlGenerator->generate('sumex/index')],
                                ['label' =>'Merchant','url'=>$urlGenerator->generate('merchant/index')],
                                ['label' =>'Custom Invoice','url'=>$urlGenerator->generate('invcust/index')], 
                                ['label' =>'Custom Field','url'=>$urlGenerator->generate('customfield/index')],
                                ['label' =>'Custom Value','url'=>$urlGenerator->generate('customvalue/index')],
                               ]
                ],              
            ]       
        );

echo Nav::widget()
        ->currentPath($urlMatcher->getCurrentUri()->getPath())
        ->options(['class' => 'navbar-nav'])
        ->items(
            $user->getId() === null
                ? [
                ['label' => 'Login', 'url' => $urlGenerator->generate('site/login')],
                ['label' => 'Signup', 'url' => $urlGenerator->generate('site/signup')],
            ]
                : [Form::widget()
                    ->action($urlGenerator->generate('site/logout'))
                    ->options(['csrf' => $csrf])
                    ->begin()
                    . Html::submitButton('Logout (' . Html::encode($user->getLogin()) . ')', ['class' => 'dropdown-item'])
                    . Form::end()],
        );
echo NavBar::end();

?><main class="container py-4">
<?php if ($user->getId() <> null) {
    echo Breadcrumbs::widget()->links([
                                   ['label' => 'Generator Add','url'=>$urlGenerator->generate('generator/add')],
                                   ['label' => 'Generator Relation Add','url'=>$urlGenerator->generate('generatorrelation/add')],
                                   ['label' => 'Setting Add','url'=>$urlGenerator->generate('setting/add')],
                                   ['label' => 'Client Add','url'=>$urlGenerator->generate('client/add')],
                                   ['label' => 'Client Custom Add','url'=>$urlGenerator->generate('clientcustom/add')],
                                   ['label' => 'Client Note Add','url'=>$urlGenerator->generate('clientnote/add')], 
                                   ['label' => 'Email Template Add','url'=>$urlGenerator->generate('emailtemplate/add')],
                                   ['label' => 'Family Add','url'=>$urlGenerator->generate('family/add')],
                                   ['label' => 'Tax Rate Add','url'=>$urlGenerator->generate('taxrate/add')],
                                   ['label' => 'Unit Add','url'=>$urlGenerator->generate('unit/add')],
                                   ['label' => 'Product Add','url'=>$urlGenerator->generate('product/add')],
                                   ['label' => 'Project Add','url'=>$urlGenerator->generate('project/add')],
                                   ['label' => 'Task Add','url'=>$urlGenerator->generate('task/add')],
                                   ['label' => 'Group Add','url'=>$urlGenerator->generate('group/add')],
                                   ['label' => 'Invoice Add','url'=>$urlGenerator->generate('inv/add')],
                                   ['label' => 'Invoice Item Add','url'=>$urlGenerator->generate('item/add')],
                                   ['label' => 'Invoice Amount Add','url'=>$urlGenerator->generate('amount/add')],
                                   ['label' => 'Sumex Add','url'=>$urlGenerator->generate('sumex/add')],
                                   ['label' => 'Merchant Add','url'=>$urlGenerator->generate('merchant/add')],
                                   ['label' => 'Custom Invoice Add','url'=>$urlGenerator->generate('invcust/add')],
                                   ['label' => 'Custom Field Add','url'=>$urlGenerator->generate('customfield/add')],
                                   ['label' => 'Custom Value Add','url'=>$urlGenerator->generate('customvalue/add')] 
                                  ])
                          ->activeItemTemplate("<li class=\"breadcrumb-item active\" aria-current=\"page\">{link}</li>\n")
                          ->homelink(['label'=>'Home','url'=>$urlGenerator->generate('invoice/index')]);
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
