<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
/**
 * @var \App\Invoice\Entity\EmailTemplate $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var bool $canEdit
 * @var string $email_template_id
 * @var \Yiisoft\Session\Flash\Flash $flash 
 */
?>
    <h1><?= Html::encode('Email Templates'); ?></h1>
    <?php
      if (!empty($flash)) {
        $danger = $flash->get('danger');
        if ($danger !== null) {
            $alert =  Alert::widget()
                ->body($danger)
                ->options([
                    'class' => ['alert-danger shadow'],
                ])
            ->render();
            echo $alert;
        }
        $info = $flash->get('info');
        if ($info !== null) {
            $alert =  Alert::widget()
                ->body($info)
                ->options([
                    'class' => ['alert-info shadow'],
                ])
            ->render();
            echo $alert;
        }
        $warning = $flash->get('warning');
        if ($warning !== null) {
            $alert =  Alert::widget()
                ->body($warning)
                ->options([
                    'class' => ['alert-warning shadow'],
                ])
            ->render();
            echo $alert;
        }
      }
    ?>
    <div>        
        <?php
        if ($canEdit) {
            echo Html::a('Add Email Template',
                $urlGenerator->generate('emailtemplate/add'),
                ['class' => 'btn btn-outline-secondary btn-md-12 mb-3']
            );
            //list all the email templates
            foreach ($emailtemplates as $emailtemplate){
                echo Html::br();
                $label = $emailtemplate->id . " ";
                echo Html::label($label);
                echo Html::a($emailtemplate->email_template_title,$urlGenerator->generate('emailtemplate/view',['email_template_id' => $emailtemplate->id]),['class' => 'btn btn-success btn-sm ms-2']);
                echo Html::a($s->trans('edit'),
                $urlGenerator->generate('emailtemplate/edit', ['email_template_id' => $emailtemplate->id]),
                ['class' => 'btn btn-info btn-sm ms-2']
                );                
                echo Html::a($s->trans('view'),
                $urlGenerator->generate('emailtemplate/view',['email_template_id' => $emailtemplate->id]),
                ['class' => 'btn btn-warning btn-sm ms-2']
                );
                echo Html::a($s->trans('delete'),
                $urlGenerator->generate('emailtemplate/delete',['email_template_id' => $emailtemplate->id]),
                ['class' => 'btn btn-danger btn-sm ms-2']
                );
                echo Html::br();
            }           
        }
        ?>
    </div>
<?php
echo Html::closeTag('div');