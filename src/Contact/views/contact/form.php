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
                            ->attributes(['enctype' => 'multipart/form-data'])
                            ->csrf($csrf)
                            ->id('form-contact')
                            ->begin()
                        ?>

                            <?= Field::widget()->config($form, 'name') ?>
                            <?= Field::widget()->config($form, 'email')->email() ?>
                            <?= Field::widget()->config($form, 'subject') ?>
                            <?= Field::widget()->config($form, 'body')->textArea(['class' => 'h-100']) ?>
                            <?= Field::widget()
                                ->config($form, 'attachFiles')
                                ->containerClass('mb-3')
                                ->file(
                                    ['type' => 'file', 'multiple' => 'multiple', 'name' => 'attachFiles[]'],
                                    true,
                                )
                                ->label([], null)
                            ?>
                            <div class="btn-group btn-toolbar float-end">
                                <?= ResetButton::widget()
                                    ->attributes(['class' => 'btn btn-danger btn-lg'])
                                    ->id('reset-button')
                                    ->value($translator->translate('layout.reset'))
                                ?>
                                <?= SubmitButton::widget()
                                    ->attributes(['class' => 'btn btn-primary btn-lg'])
                                    ->id('contact-button')
                                    ->value($translator->translate('layout.submit'))
                                ?>
                            </div>
                        <?= Form::end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
