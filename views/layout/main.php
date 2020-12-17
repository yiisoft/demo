<?php

declare(strict_types=1);

use App\Asset\AppAsset;
use App\Widget\PerformanceMetrics;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Html\Html;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Yii\Bootstrap5\Nav;
use Yiisoft\Yii\Bootstrap5\NavBar;

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
    AppAsset::class
]);

$this->setCssFiles($assetManager->getCssFiles());
$this->setJsFiles($assetManager->getJsFiles());
$this->setJsStrings($assetManager->getJsStrings());
$this->setJsVar($assetManager->getJsVar());

$currentRoute = $urlMatcher->getCurrentRoute() === null ? '' : $urlMatcher->getCurrentRoute()->getName();

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

echo NavBar::widget()
      ->brandLabel($brandLabel)
      ->brandUrl($urlGenerator->generate('site/index'))
      ->options(['class' => 'navbar navbar-light bg-light navbar-expand-sm text-white'])
      ->begin();
echo Nav::widget()
        ->currentPath($urlMatcher->getCurrentUri()->getPath())
        ->options(['class' => 'navbar-nav mx-auto'])
        ->items(
            [
                ['label' => 'Blog', 'url' => $urlGenerator->generate('blog/index'), 'active' => StringHelper::startsWith($currentRoute, 'blog/') && $currentRoute !== 'blog/comment/index'],
                ['label' => 'Comments Feed', 'url' => $urlGenerator->generate('blog/comment/index')],
                ['label' => 'Users', 'url' => $urlGenerator->generate('user/index'), 'active' => StringHelper::startsWith($currentRoute, 'user/')],
                ['label' => 'Contact', 'url' => $urlGenerator->generate('site/contact')],
                ['label' => 'Swagger', 'url' => $urlGenerator->generate('swagger/index')],
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

?><main role="main" class="container py-4"><?php
echo $content;
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
