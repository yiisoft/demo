<?php

declare(strict_types=1);

use Yiisoft\Form\Field;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Form;

/**
 * @var Yiisoft\View\WebView $this
 * @var Yiisoft\Translator\TranslatorInterface $translator
 * @var Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var string $csrf
 * @var App\Modules\Auth\Form\LoginForm $formModel
 */

$this->setTitle($translator->translate('layout.login'));

$error = $error ?? null;
?>

<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card border border-dark shadow-2-strong rounded-3">
                <div class="card-header bg-dark text-white">
                    <h1 class="fw-normal h3 text-center"><?= Html::encode($this->getTitle()) ?></h1>
                </div>
                <div class="card-body p-5 text-center">
                    <?= Form::tag()
                        ->post($urlGenerator->generate('auth/login'))
                        ->csrf($csrf)
                        ->id('loginForm')
                        ->open() ?>

                    <?= Field::text($formModel, 'login')->autofocus() ?>
                    <?= Field::password($formModel, 'password') ?>
                    <?= Field::checkbox($formModel, 'rememberMe')
                        ->containerClass('form-check form-switch text-start mt-2')
                        ->inputClass('form-check-input')
                        ->inputLabelClass('form-check-label') ?>
                    <?= Field::submitButton()
                        ->buttonId('login-button')
                        ->name('login-button')
                        ->content($translator->translate('layout.submit')) ?>

                    <?= Form::tag()->close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

