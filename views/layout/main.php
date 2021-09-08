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
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Assets\AssetManager $assetManager
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var string $content
 *
 * @see \App\ApplicationViewInjection
 * @var \App\User\User $user
 * @var string $csrf
 * @var string $brandLabel
 */

$assetManager->register([
    AppAsset::class,
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
    ->options(['class' => 'navbar-nav mx-auto'])
    ->items(
        [
            ['label' => $translator->translate('menu.blog'), 'url' => $urlGenerator->generate('blog/index'), 'active' => StringHelper::startsWith($currentRouteName, 'blog/') && $currentRouteName !== 'blog/comment/index'],
            ['label' => $translator->translate('menu.comments_feed'), 'url' => $urlGenerator->generate('blog/comment/index')],
            ['label' => $translator->translate('menu.users'), 'url' => $urlGenerator->generate('user/index'), 'active' => StringHelper::startsWith($currentRouteName, 'user/')],
            ['label' => $translator->translate('menu.contact'), 'url' => $urlGenerator->generate('site/contact')],
            ['label' => $translator->translate('menu.swagger'), 'url' => $urlGenerator->generate('swagger/index')],
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

?>
<main class="container py-4"><?= $content ?></main>

<footer class="container py-4">
    <form id="localeForm" method="POST" action="<?= $urlGenerator->generate('site/set-locale') ?>" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <div class="col-2 d-inline-block">
            <select name="locale" class="form-select" aria-label="<?= $translator->translate('layout.change_language') ?>">
                <option value="en"><?= $translator->translate('layout.language.english') ?></option>
                <option value="ru"><?= $translator->translate('layout.language.russian') ?></option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary"><?= $translator->translate('layout.change_language') ?></button>
    </form>
    <?= PerformanceMetrics::widget() ?>
</footer>
<?php

$this->endBody();
?>
</body>
</html>
<?php
$this->endPage(true);
