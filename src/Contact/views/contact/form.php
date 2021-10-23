<?php

declare(strict_types=1);

use App\Widget\FlashMessage;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Html\Html;
use Yiisoft\View\WebView;

/**
 * @var Yiisoft\Csrf\Csrf $csrf
 * @var \App\Contact\ContactForm $form
 * @var \Yiisoft\Router\UrlGeneratorInterface $url
 * @var \Yiisoft\Form\Widget\Field $field
 * @var WebView $this
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 */

$this->setTitle($translator->translate('menu.contact'));
?>

<h1><?= Html::encode($this->getTitle()) ?></h1>

<?= FlashMessage::widget() ?>

<div>

    <?= Form::widget()
        ->action($url->generate('site/contact'))
        ->attributes(['enctype' => 'multipart/form-data'])
        ->id('form-contact')
        ->begin() ?>
    <?= $csrf->hiddenInput() ?>

    <?= $field->config($form, 'name') ?>
    <?= $field->config($form, 'email')->email() ?>
    <?= $field->config($form, 'subject') ?>
    <?= $field->config($form, 'body')
        ->textArea(['class' => 'form-control textarea', 'rows' => 2]) ?>
    <?= $field->config($form, 'attachFiles')
        ->inputClass('form-control')
        ->file(
            ['type' => 'file', 'multiple' => 'multiple', 'name' => 'attachFiles[]'],
            true,
        ) ?>
    <?= $field->submitButton(
        [
            'class' => 'btn btn-primary btn-lg mt-3',
            'id' => 'contact-button',
            'value' => 'Submit'
        ],
    ) ?>

    <?= Form::end() ?>
</div>
