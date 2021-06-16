<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
/**
 * @var \App\Invoice\Entity\Setting $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var bool $canEdit
 * @var string $setting_id
 * @var \Yiisoft\Session\Flash\Flash $flash 
 */

?>
    <h1><?= Html::encode($s->trans('settings')); ?></h1>
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
            echo Html::a('Add Setting',
                $urlGenerator->generate('setting/add'),
                ['class' => 'btn btn-outline-secondary btn-md-12 mb-3']
            );
            //list all the settings
            foreach ($settings as $setting){
                echo Html::br();
                $label = $setting->id . " ";
                echo Html::label($label);
                echo Html::a($setting->setting_key." ". $setting->setting_value,$urlGenerator->generate('setting/view',['setting_id' => $setting->id]),['class' => 'btn btn-success btn-sm ms-2']);
                echo Html::a($s->trans('edit'),
                $urlGenerator->generate('setting/edit', ['setting_id' => $setting->id]),
                ['class' => 'btn btn-info btn-sm ms-2']
                );                
                echo Html::a($s->trans('view'),
                $urlGenerator->generate('setting/view',['setting_id' => $setting->id]),
                ['class' => 'btn btn-warning btn-sm ms-2']
                );
                echo Html::a($s->trans('delete'),
                $urlGenerator->generate('setting/delete',['setting_id' => $setting->id]),
                ['class' => 'btn btn-danger btn-sm ms-2']
                );
                echo Html::br();
            }           
        }
        ?>
    </div>
<?php
echo Html::closeTag('div');