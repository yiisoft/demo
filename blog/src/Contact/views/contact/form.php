<?php

declare(strict_types=1);

use App\Contact\ContactForm;
use App\Widget\FlashMessage;
use Yiisoft\Form\Field;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

/**
 * @var Yiisoft\Yii\View\Csrf $csrf
 * @var ContactForm           $form
 * @var UrlGeneratorInterface $url
 * @var WebView               $this
 * @var TranslatorInterface   $translator
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
                    <?= Form::tag()
                        ->post($url->generate('site/contact'))
                        ->enctypeMultipartFormData()
                        ->csrf($csrf)
                        ->id('form-contact')
                        ->open()
                    ?>

                    <?= Field::text($form, 'name') ?>
                    <?= Field::email($form, 'email') ?>
                    <?= Field::text($form, 'subject') ?>
                    <?= Field::textarea($form, 'body')->addInputAttributes(['style' => 'height: 100px']) ?>
                    <?= Field::file($form, 'attachFiles[]')
                        ->containerClass('mb-3')
                        ->multiple()
                        ->hideLabel()
                    ?>
                    <?= Field::buttonGroup()
                        ->addContainerClass('btn-group btn-toolbar float-end')
                        ->buttonsData([
                            [
                                $translator->translate('layout.reset'),
                                'type' => 'reset',
                                'class' => 'btn btn-lg btn-danger',
                            ],
                            [
                                $translator->translate('layout.submit'),
                                'type' => 'submit',
                                'class' => 'btn btn-lg btn-primary',
                                'name' => 'contact-button',
                            ],
                        ]) ?>

                    <?= Form::tag()->close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
