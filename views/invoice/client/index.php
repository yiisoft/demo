<?php

declare(strict_types=1);

/**
 * @var \App\Blog\Entity\Client $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var bool $canEdit
 * @var string $client_id
 */

use Yiisoft\Html\Html;
?>
    <h1><?= Html::encode('Clients') ?></h1>
    <div>        
        <?php
        if ($canEdit) {
            echo Html::a(
                'Add Client',
                $urlGenerator->generate('client/add'),
                ['class' => 'btn btn-outline-secondary btn-md-12 mb-3']
            );
            //list all the clients
            foreach ($clients as $client){
                echo Html::br();
                $label = $client->client_id . " ";
                echo Html::label($label);
                echo Html::a($client->client_name." ". $client->client_surname,'',['class' => 'btn btn-success btn-sm ms-2']);
                echo Html::a(
                'Edit',
                $urlGenerator->generate('client/edit', ['client_id' => $client->client_id]),
                ['class' => 'btn btn-info btn-sm ms-2']
                );
                 echo Html::a(
                'Delete',
                $urlGenerator->generate('client/delete', ['client_id' => $client->client_id]),
                ['class' => 'btn btn-danger btn-sm ms-2']
                );
                echo Html::br();
            }           
        }
        ?>
    </div>
<?php
echo Html::closeTag('div');