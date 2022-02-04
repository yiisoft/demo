<?php

declare(strict_types=1);

use App\Widget\FlashMessage;
use Yiisoft\Form\Widget\Field;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Form\Widget\ResetButton;
use Yiisoft\Form\Widget\SubmitButton;
use Yiisoft\Html\Html;
use Yiisoft\View\WebView;

/**
 * @var Yiisoft\Yii\View\Csrf $csrf
 * @var \App\Contact\ContactForm $form
 * @var \Yiisoft\Router\UrlGeneratorInterface $url
 * @var \Yiisoft\Form\Widget\Field $field
 * @var WebView $this
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 */

$this->setTitle($translator->translate('menu.contact'));
?>

<?= FlashMessage::widget() ?>

<div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-8">
            <div class="card border border-dark shadow-2-strong rounded-3">
                <div class="card-header bg-dark text-white">
                    <h1 class="fw-normal h3 text-center"><?= Html::encode($this->getTitle()) ?></h1>
                </div>
                    <div class="card-body p-5 text-center">
                        <?= Form::widget()
                            ->action($url->generate('site/contact'))
                            ->csrf($csrf)
                            ->id('form-contact')
                            ->begin()
                        ?>

                            <?= Field::widget()->text($form, 'name') ?>
                            <?= Field::widget()->email($form, 'email') ?>
                            <?= Field::widget()->text($form, 'subject') ?>
                            <?= Field::widget()->attributes(['style' => 'height: 100px'])->textArea($form, 'body') ?>
                            <?= Field::widget()
                                ->containerClass('mb-3')
                                ->file($form, 'attachFiles', ['multiple()' => [true]])
                                ->label(null)
                            ?>
                            <?= Field::widget()
                                ->containerClass('btn-group btn-toolbar float-end')
                                ->buttonGroup(
                                    [
                                        ['label' => 'Reset', 'type' => 'reset'],
                                        ['label' => 'Submit', 'type' => 'submit'],
                                    ],
                                        ['individualButtonAttributes()' => [
                                            [
                                                0 => ['class' => 'btn btn-lg btn-danger'],
                                                1 => ['class' => 'btn btn-lg btn-primary', 'name' => 'contact-button'],
                                            ],
                                        ],
                                    ],
                                )
                            ?>

                        <?= Form::end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
