<?php
declare(strict_types=1);

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
?>
<?
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
?>
<!DOCTYPE html>
<html class="no-js" lang="<?= $s->trans('cldr'); ?>">

<head>
    <title>
        <?php
        if ($s->get_setting('custom_title') != '') {
            echo $s->get_setting('custom_title', '', true);
        } else {
            echo 'Invoice';
        } ?>
    </title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="NOINDEX,NOFOLLOW">
    <meta name="_csrf" content="<?= $csrf; ?>">
</head>
<body>
<?php
$this->beginBody();
echo NavBar::widget()
      ->brandText($brandLabel)
      ->brandUrl($urlGenerator->generate('site/index'))
      ->options(['class' => 'navbar navbar-light bg-light navbar-expand-sm text-white'])
      ->begin();
?>
<?php
    echo NavBar::widget()
    ->brandText($brandLabel)
    ->brandUrl($urlGenerator->generate('site/index'))
    ->options(['class' => 'navbar navbar-light bg-light navbar-expand-sm text-white'])
    ->begin();
?>        
<?php 
    echo Nav::widget()
    ->currentPath($currentRoute->getUri()->getPath())
    ->options(['class' => 'navbar-nav mx-auto', 'style'=>'background-color: #e3f2fd;'])
    ->items( 
        $isGuest
            ? [
            ['label' => $s->trans('dashboard'), 'url' => $urlGenerator->generate('guest/index')],
            ['label' => $s->trans('quotes'), 'url' => $urlGenerator->generate('guest/quotes/index')],
            ['label' => $s->trans('invoices'), 'url' => $urlGenerator->generate('guest/invoices/index')],
            ['label' => $s->trans('payments'), 'url' => $urlGenerator->generate('guest/payments/index')],
        ] :
        []);
?>
<?php
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
                    $isGuest ? '' : Form::widget()
                        ->action($urlGenerator->generate('auth/logout'))
                        ->csrf($csrf)
                        ->begin()
                    . Field::widget()
                        ->attributes(['class' => 'btn btn-primary'])
                        ->containerClass('mb-1')
                        ->submitButton()
                        ->value($translator->translate('menu.logout', ['login' => Html::encode($user->getLogin())]))
                    . Form::end()
                ],
                );
?>
<?php echo NavBar::end();?>
<?php
    Html::a(
                Html::tag(
                  'span','&nbsp;'.$s->trans('logout'),['class'=>'visible-xs'])
                .
                Html::tag(
                  'i', '', ['class' => 'fa fa-power-off','style' => 'font-size: 1.5em;']
                ), 
               $urlGenerator->generate('auth/logout'), ['class' => 'tip icon logout','data-placement'=>'bottom','title'=>$s->trans('logout')]
    );                        
?>
<?php
$this->endBody();
?>
</body>
</html>
<?php
$this->endPage(true);
?>