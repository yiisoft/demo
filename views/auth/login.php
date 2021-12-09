<?php

declare(strict_types=1);

use Yiisoft\Form\FormModelInterface;
use Yiisoft\Form\Widget\Field;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var string $csrf
 * @var FormModelInterface $formModel
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
                    <?= Form::widget()
                        ->action($urlGenerator->generate('auth/login'))
                        ->attributes(['enctype' => 'multipart/form-data', 'novalidate' => true])
                        ->csrf($csrf)
                        ->id('loginForm')
                        ->begin() ?>

                        <?= Field::widget()->config($formModel, 'login')->text(['autofocus' => true]) ?>
                        <?= Field::widget()->config($formModel, 'password')->password() ?>
                        <?= Field::widget()->config($formModel, 'rememberMe')
                            ->containerClass('form-check form-switch text-start mt-2')
                            ->inputClass('form-check-input')
                            ->checkbox([], false)
                            ->labelClass('form-check-label')
                        ?>
                        <?= Field::widget()->containerClass('d-grid gap-2 form-floating')->submitButton(
                            [
                                'class' => 'btn btn-primary btn-lg mt-3',
                                'id' => 'login-button',
                                'value' => $translator->translate('layout.submit'),
                            ]
                        ) ?>
                    <?= Form::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

