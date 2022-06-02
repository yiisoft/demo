<?php
declare(strict_types=1);

use Yiisoft\Yii\Bootstrap5\Alert;
use Yiisoft\Html\Html;
use App\Invoice\Helpers\ClientHelper;

/**
 * @var Yiisoft\Yii\View\Csrf $csrf 
 * @var \Yiisoft\Session\Flash\FlashInterface $flash
 */
$client_helper = new ClientHelper();
?>
<form method="post">

    <input type="hidden" name="_csrf"
           value="<?= $csrf; ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?= $s->trans('assign_client'); ?></h1>
        <?php
            $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]);
            echo (string)$response->getBody();
        ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">
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
                <input type="hidden" name="user_id" id="user_id"
                       value="<?= $userinv->getUser_id(); ?>">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= Html::encode($userinv->getName()); ?>
                    </div>
                    <div class="panel-body">
                    
                        <div class="alert alert-info">
                            <label for="user_all_clients">
                                <?php $body['user_all_clients'] = $user_all_clients; ?>
                                <input type="checkbox" name="user_all_clients" id="user_all_clients" value="1" <?php $s->check_select(Html::encode($body['user _all_clients'] ?? ''), 1, '==', true) ?>>
                                <?= $s->trans('user_all_clients') ?>
                            </label>                                
                            <div>
                                <?= $s->trans('user_all_clients_text') ?>
                            </div>
                        </div>
                        
                        <div id="list_client">
                            <label for="client_id"><?= $s->trans('client'); ?></label>
                            <select name="client_id" id="client_id" class="form-control" autofocus="autofocus">
                                <?php foreach ($clients as $client) { ?>
                                    <option value="<?= $client->getClient_id(); ?>"
                                     <?php $s->check_select(Html::encode($body['client_id'] ?? ''), $client->getClient_id()) ?>
                                     ><?= Html::encode($client_helper->format_client($client)); ?></option>
                                <?php } ?>     
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>