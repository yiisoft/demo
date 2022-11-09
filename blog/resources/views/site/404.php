<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

/**
 * @var TranslatorInterface   $translator
 * @var UrlGeneratorInterface $urlGenerator
 * @var CurrentRoute          $currentRoute
 * @var WebView               $this
 */
$this->setTitle($translator->translate('layout.not-found'));
?>

<div class="card shadow p-5 my-5 mx-5 bg-white rounded">
    <div class="card-body text-center ">
        <h1 class="card-title display-1 fw-bold">404</h1>
        <p class="card-text">
            <?= $translator->translate('layout.page.not-found', [
                'url' => Html::span(
                    Html::encode($currentRoute
                        ->getUri()
                        ->getPath()),
                    ['class' => 'text-muted']
                ),
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
