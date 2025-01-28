<?php

declare(strict_types=1);

use App\Asset\AppAsset;
use App\User\User;
use App\Widget\PerformanceMetrics;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Button;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Bootstrap5\Dropdown;
use Yiisoft\Yii\Bootstrap5\DropdownItem;
use Yiisoft\Yii\Bootstrap5\Nav;
use Yiisoft\Yii\Bootstrap5\NavBar;
use Yiisoft\Yii\Bootstrap5\NavLink;

/**
 * @var UrlGeneratorInterface $urlGenerator
 * @var CurrentRoute          $currentRoute
 * @var WebView               $this
 * @var AssetManager          $assetManager
 * @var TranslatorInterface   $translator
 * @var string                $content
 *
 * @see \App\ApplicationViewInjection
 *
 * @var User|null $user
 * @var string    $csrf
 * @var string    $brandLabel
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
            ->begin() ?>

        <?= Nav::widget()
            ->addClass('navbar-nav mx-auto')
            ->currentPath($currentRoute->getUri()->getPath())
            ->items(
                NavLink::to(
                    $translator->translate('menu.blog'),
                    $urlGenerator->generate('blog/index', ['_language' => $translator->getLocale()]),
                ),
                NavLink::to(
                    $translator->translate('menu.comments-feed'),
                    $urlGenerator->generate('blog/comment/index', ['_language' => $translator->getLocale()]),
                ),
                NavLink::to(
                    $translator->translate('menu.users'),
                    $urlGenerator->generate('user/index', ['_language' => $translator->getLocale()]),
                ),
                NavLink::to(
                    $translator->translate('menu.contact'),
                    $urlGenerator->generate('site/contact', ['_language' => $translator->getLocale()]),
                ),
                NavLink::to(
                    $translator->translate('menu.swagger'),
                    $urlGenerator->generate('swagger/index'),
                ),
            ) ?>

        <?= Nav::widget()
            ->currentPath($currentRoute->getUri()->getPath())
            ->items(
                Dropdown::widget()
                    ->items(
                        DropdownItem::link(
                            'English',
                            $urlGenerator->generateFromCurrent(['_language' => 'en'], fallbackRouteName: 'site/index'),
                        ),
                        DropdownItem::link(
                            'Русский',
                            $urlGenerator->generateFromCurrent(['_language' => 'ru'], fallbackRouteName: 'site/index'),
                        ),
                        DropdownItem::link(
                            'Slovenský',
                            $urlGenerator->generateFromCurrent(['_language' => 'sk'], fallbackRouteName: 'site/index'),
                        ),
                        DropdownItem::link(
                            'Indonesia',
                            $urlGenerator->generateFromCurrent(['_language' => 'id'], fallbackRouteName: 'site/index'),
                        ),
                        DropdownItem::link(
                            'German',
                            $urlGenerator->generateFromCurrent(['_language' => 'de'], fallbackRouteName: 'site/index'),
                        ),
                    )
                    ->toggleContent($translator->translate('menu.language')),
                NavLink::to(
                    $translator->translate('menu.login'),
                    $urlGenerator->generate('auth/login'),
                    visible: $isGuest,
                ),
                NavLink::to(
                    $translator->translate('menu.signup'),
                    $urlGenerator->generate('auth/signup'),
                    visible: $isGuest,
                ),
                NavLink::to(
                    $isGuest
                        ? ''
                        : Form::tag()
                            ->post($urlGenerator->generate('auth/logout'))
                            ->csrf($csrf)
                            ->open()
                            . '<div class="mb-1">'
                            . Button::submit(
                                $translator->translate('menu.logout', ['login' => Html::encode($user->getLogin())])
                            )
                            ->class('btn btn-primary')
                            . '</div>'
                            . Form::tag()->close(),
                        encodeLabel: false,
                ),
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
$this->endPage();
