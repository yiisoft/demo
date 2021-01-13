<?php

declare(strict_types=1);

/**
 * @var $csrf string
 * @var $form Yiisoft\Form\FormModel
 * @var $url \Yiisoft\Router\UrlGeneratorInterface
 * @var $field \Yiisoft\Form\Widget\Field
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
