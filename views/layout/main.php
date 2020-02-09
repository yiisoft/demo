<?php

use App\Asset\AppAsset;
use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap4\Nav;
use Yiisoft\Yii\Bootstrap4\NavBar;

/**
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 * @var \App\Entity\User $user
 * @var \Yiisoft\Assets\AssetManager $assetManager
 * @var string $content
 * @var null|string $currentUrl
 */

$assetManager->register([
    AppAsset::class
]);

$this->setCssFiles($assetManager->getCssFiles());
$this->setJsFiles($assetManager->getJsFiles());

$this->beginPage();
?><!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yii Demo</title>
    <?php $this->head() ?>
</head>
<body>
<?php
$this->beginBody();

echo NavBar::begin()
      ->brandLabel('Yii Demo')
      ->brandUrl($urlGenerator->generate('site/index'))
      ->options(
          [
              'class' => 'navbar navbar-light bg-light navbar-expand-sm text-white',
          ]
      )->start();
echo Nav::widget()
        ->currentPath($currentUrl ?? '')
        ->items(
            [
                ['label' => 'Blog', 'url' => $urlGenerator->generate('blog/index')],
                ['label' => 'Users', 'url' => $urlGenerator->generate('user/index')],
                ['label' => 'Contact', 'url' => $urlGenerator->generate('site/contact')],
            ]
        )
        ->options(
            [
                'class' => 'navbar-nav mr-auto',
            ]
        );
echo Nav::widget()
        ->currentPath($currentUrl ?? '')
        ->items(
            [
                $user->getId() === null
                    ? ['label' => 'Login', 'url' => $urlGenerator->generate('site/login')]
                    : ['label' => "Logout ({$user->getLogin()})", 'url' => $urlGenerator->generate('site/logout')],
            ]
        )
        ->options(
            [
                'class' => 'navbar-nav',
            ]
        );
echo NavBar::end();

echo Html::beginTag('main', ['role' => 'main', 'class' => 'container py-4']);
echo $content;
echo Html::endTag('main');

$this->endBody();
?>
</body>
</html>
<?php
$this->endPage(true);
