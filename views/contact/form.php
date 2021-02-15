<?php

declare(strict_types=1);

/**
 * @var string $csrf
 * @var \App\Contact\ContactForm $form
 * @var \Yiisoft\Router\UrlGeneratorInterface $url
 * @var \Yiisoft\Form\Widget\Field $field
 */

use App\Widget\FlashMessage;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Html\Html;

?>

<h1>Contact</h1>

<?= FlashMessage::widget() ?>

<div>

    <?= Form::widget()
        ->action($url->generate('site/contact'))
        ->options(
            [
                'id' => 'form-contact',
                'csrf' => $csrf,
                'enctype' => 'multipart/form-data',
            ]
        )
        ->begin() ?>

    <?= $field->config($form, 'name') ?>
    <?= $field->config($form, 'email')->input('email') ?>
    <?= $field->config($form, 'subject') ?>
    <?= $field->config($form, 'body')
        ->textArea(['class' => 'form-control textarea', 'rows' => 2]) ?>
    <?= $field->config($form, 'attachFiles')
        ->inputCssClass('form-control')
        ->fileInput(
            ['type' => 'file', 'multiple' => 'multiple', 'name' => 'attachFiles[]'],
            true,
        ) ?>

    <?= Html::submitButton(
            'Submit',
            [
            'class' => 'btn btn-primary mt-3'
        ]
        ) ?>

    <?= Form::end() ?>

</div>
