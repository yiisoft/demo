<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
/**
 * @var \App\Blog\Entity\Client $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var bool $canEdit
 * @var string $client_id
 * @var \Yiisoft\Session\Flash\FlashInterface $flash 
 */


?>
    <h1><?= Html::encode($s->trans('clients')); ?></h1>
    <?php
        $danger = $flash->get('danger');
        if ($danger != null) {
            $alert =  Alert::widget()
                ->body($danger)
                ->options([
                    'class' => ['alert-danger shadow'],
                ])
            ->render();
            echo $alert;
        }
        $info = $flash->get('info');
        if ($info != null) {
            $alert =  Alert::widget()
                ->body($info)
                ->options([
                    'class' => ['alert-info shadow'],
                ])
            ->render();
            echo $alert;
        }
        $warning = $flash->get('warning');
        if ($warning != null) {
            $alert =  Alert::widget()
                ->body($warning)
                ->options([
                    'class' => ['alert-warning shadow'],
                ])
            ->render();
            echo $alert;
        }
    ?>
    <div>        
        <?php
        if ($canEdit) {
            echo Html::a($s->trans('add_client'),
                $urlGenerator->generate('client/add'),
                ['class' => 'btn btn-outline-secondary btn-md-12 mb-3']
            );
            //list all the clients
            foreach ($clients as $client){
                echo Html::br();
                $label = $client->id . " ";
                echo Html::label($label);
                echo Html::a($client->client_name." ". $client->client_surname,$urlGenerator->generate('client/view',['client_id' => $client->id]),['class' => 'btn btn-success btn-sm ms-2']);
                echo Html::a($s->trans('edit'),
                $urlGenerator->generate('client/edit', ['client_id' => $client->id]),
                ['class' => 'btn btn-info btn-sm ms-2']
                );                
                echo Html::a($s->trans('view'),
                $urlGenerator->generate('client/view',['client_id' => $client->id]),
                ['class' => 'btn btn-warning btn-sm ms-2']
                );
                echo Html::a($s->trans('delete'),
                $urlGenerator->generate('client/delete',['client_id' => $client->id]),
                ['class' => 'btn btn-danger btn-sm ms-2']
                );
                echo Html::br();
            }           
        }
        ?>
    </div>
<?php
echo Html::closeTag('div');