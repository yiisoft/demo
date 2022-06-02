<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRoute $currentRoute
 * @var \Yiisoft\View\WebView $this
 */

$this->setTitle($translator->translate('layout.not-found'));
?>

<div class="card shadow p-5 my-5 mx-5 bg-white rounded">
    <div class="card-body text-center ">
        <h1 class="card-title display-1 fw-bold">404</h1>
        <p class="card-text">
            <?= $translator->translate('layout.page.not-found', [
                'url' => Html::span(
                    Html::encode($currentRoute->getUri()->getPath()),
                    ['class' => 'text-muted']
                )
            ])
            ?>
        </p>
        <p>
            <?= Html::a(
                $translator->translate('layout.go.home'),
                $urlGenerator->generate('site/index'),
                ['class' => 'btn btn-outline-primary mt-5']
            );
            ?>
        </p>
    </div>
</div>
