<?php

declare(strict_types=1);

use App\Asset\AppAsset;
use App\Widget\PerformanceMetrics;
use Yiisoft\Form\Widget\Field;
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
 * @var \App\User\User|null $user
 * @var string $csrf
 * @var string $brandLabel
 */

$assetManager->register(AppAsset::class);

$this->addCssFiles($assetManager->getCssFiles());
$this->addCssStrings($assetManager->getCssStrings());
$this->addJsFiles($assetManager->getJsFiles());
$this->addJsStrings($assetManager->getJsStrings());
$this->addJsVars($assetManager->getJsVars());

$currentRouteName = $currentRoute->getName() ?? '';
$isGuest = $user === null || $user->getId() === null;

$this->beginPage();
?>
<!DOCTYPE html>
<html class="h-100" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Yii Demo<?= $this->getTitle() ? ' - ' . Html::encode($this->getTitle()) : '' ?></title>
        <?php $this->head() ?>
    </head>
    <body class="cover-container-fluid d-flex w-100 h-100 mx-auto flex-column">
        <header class="mb-auto">
            <?php $this->beginBody() ?>

            <?= NavBar::widget()
                ->brandText($brandLabel)
                ->brandUrl($urlGenerator->generate('site/index'))
                ->options(['class' => 'navbar navbar-light bg-light navbar-expand-sm text-white'])
                ->begin() ?>

            <?= Nav::widget()
                ->currentPath($currentRoute->getUri()->getPath())
                ->options(['class' => 'navbar-nav mx-auto'])
                ->items(
                    [
                        [
                            'label' => $translator->translate('menu.blog'),
                            'url' => $urlGenerator->generate('blog/index'),
                            'active' => StringHelper::startsWith(
                                $currentRouteName,
                                'blog/'
                            ) && $currentRouteName !== 'blog/comment/index',
                        ],
                        [
                            'label' => $translator->translate('menu.comments-feed'),
                            'url' => $urlGenerator->generate('blog/comment/index'),
                        ],
                        [
                            'label' => $translator->translate('menu.users'),
                            'url' => $urlGenerator->generate('user/index'),
                            'active' => StringHelper::startsWith($currentRouteName, 'user/'),
                        ],
                        [
                            'label' => $translator->translate('menu.contact'),
                            'url' => $urlGenerator->generate('site/contact'),
                        ],
                        [
                            'label' => $translator->translate('menu.swagger'),
                            'url' => $urlGenerator->generate('swagger/index'),
                        ],
                    ]
                ) ?>

            <?= Nav::widget()
                ->currentPath($currentRoute->getUri()->getPath())
                ->options(['class' => 'navbar-nav'])
                ->items(
                    [
                        [
                            'label' => $translator->translate('menu.language'),
                            'url' => '#',
                            'items' => [
                                [
                                    'label' => $translator->translate('layout.language.english'),
                                    'url' => $urlGenerator->generateFromCurrent(['_language' => 'en'], 'site/index'),
                                ],
                                [
                                    'label' => $translator->translate('layout.language.russian'),
                                    'url' => $urlGenerator->generateFromCurrent(['_language' => 'ru'], 'site/index'),
                                ],
                            ],
                        ],
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
                ) ?>
            <?= NavBar::end() ?>
        </header>

        <main class="container py-3">
            <?= $content ?>
        </main>

        <footer class='mt-auto bg-dark py-3'>
            <div class = 'd-flex flex-fill align-items-center container-fluid'>
                <div class = 'd-flex flex-fill float-start'>
                    <i class=''></i>
                    <a class='text-decoration-none' href='https://www.yiiframework.com/' target='_blank' rel='noopener'>
                        Yii Framework - <?= date('Y') ?> -
                    </a>
                    <div class="ms-2 text-white">
                        <?= PerformanceMetrics::widget() ?>
                    </div>
                </div>

                <div class='float-end'>
                    <a class='text-decoration-none px-1' href='https://github.com/yiisoft' target='_blank' rel='noopener' >
                        <i class="bi bi-github text-white"></i>
                    </a>
                    <a class='text-decoration-none px-1' href='https://join.slack.com/t/yii/shared_invite/enQtMzQ4MDExMDcyNTk2LTc0NDQ2ZTZhNjkzZDgwYjE4YjZlNGQxZjFmZDBjZTU3NjViMDE4ZTMxNDRkZjVlNmM1ZTA1ODVmZGUwY2U3NDA' target='_blank' rel='noopener'>
                        <i class="bi bi-slack text-white"></i>
                    </a>
                    <a class='text-decoration-none px-1' href='https://www.facebook.com/groups/yiitalk' target='_blank' rel='noopener'>
                        <i class="bi bi-facebook text-white"></i>
                    </a>
                    <a class='text-decoration-none px-1' href='https://twitter.com/yiiframework' target='_blank' rel='noopener'>
                        <i class="bi bi-twitter text-white"></i>
                    </a>
                    <a class='text-decoration-none px-1' href='https://t.me/yii3ru' target='_blank' rel='noopener'>
                        <i class="bi bi-telegram text-white"></i>
                    </a>
                </div>
            </div>
        </footer>

        <?php $this->endBody() ?>
    </body>
</html>
<?php
$this->endPage(true);
