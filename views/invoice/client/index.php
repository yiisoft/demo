<?php

declare(strict_types=1);

/**
 * @var \App\Invoice\Entity\Client $client
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator 
 * @var \Yiisoft\Data\Paginator\OffsetPaginator $paginator
 * @var \Yiisoft\Session\Flash\FlashInterface $flash 
 */

use Yiisoft\Yii\Bootstrap5\Alert;
use App\Widget\OffsetPagination;
use Yiisoft\Html\Html;

?>
<?php
  $pagination = OffsetPagination::widget()
  ->paginator($paginator)
  ->urlGenerator(fn ($page) => $urlGenerator->generate('client/index', ['page' => $page, 'active'=>$active]));
?>
<div>
    <?php 
        echo $modal_create_client;
    ?>
</div>

<div>
    <h5><?= $s->trans('clients'); ?></h5>
    <div class="btn-group">
        <a href="#create-client" class="btn btn-success" data-toggle="modal"  style="text-decoration:none"><i class="fa fa-plus"></i> <?= $s->trans('new'); ?></a>
    </div>
    <br>
    <br>
    <div class="submenu-row">
            <div class="btn-group index-options">
                <a href="<?= $urlGenerator->generate('client/index',['page'=>1, 'active'=>2]); ?>"
                   class="btn <?php echo $active == 2 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('all'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('client/index',['page'=>1, 'active'=>1]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $active == 1 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('active'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('client/index',['page'=>1, 'active'=>0]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $active == 0 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('inactive'); ?>
                </a>    
            </div>
    </div>
</div>
<div>
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
</div>
<div id="content" class="table-content">
    <?php 
                if ($pagination->isRequired()) {
                   echo $pagination;
                }
    ?>  
        <div class="table-responsive">
        <table class="table table-hover table-striped">
        <thead>
        <tr>
        <th><?= $s->trans('active'); ?></th>
        <th><?= $s->trans('client_name'); ?></th>
        <th><?= $s->trans('email_address'); ?></th>
        <th><?= $s->trans('phone_number'); ?></th>
        <th class="amount"><?= $s->trans('balance'); ?></th>
        <th><?= $s->trans('options'); ?></th>
        </tr>
        </thead>
        <tbody>
            <?php /** @var Client $client */ ?>
            <?php foreach ($paginator->read() as $client) { ?>
            <tr>
		<td>
		    <?= ($client->getClient_active()) ? '<span class="label active">' . $s->trans('yes') . '</span>' : '<span class="label inactive">' . $s->trans('no') . '</span>'; ?>
		</td>
                <td><?= Html::a($client->getClient_name()." ".$client->getClient_surname(),$urlGenerator->generate('client/view',['id' => $client->getClient_id()]),['class' => 'btn btn-warning ms-2']);?></td>
                <td><?= Html::encode($client->getClient_email()); ?></td>
                <td><?= Html::encode($client->getClient_phone() ? $client->getClient_phone() : ($client->getClient_mobile() ? $client->getClient_mobile() : '')); ?></td>
                <td class="amount"><?php echo $s->format_currency($iR->with_total($client->getClient_id(), $iaR)); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#" style="text-decoration:none">
                            <i class="fa fa-cog"></i> <?= $s->trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?= $urlGenerator->generate('client/view',['id' => $client->getClient_id()]); ?>" style="text-decoration:none">
                                    <i class="fa fa-eye fa-margin"></i> <?= $s->trans('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $urlGenerator->generate('client/view_client_custom_fields',['id' => $client->getClient_id()]); ?>" style="text-decoration:none">
                                    <i class="fa fa-edit fa-margin"></i><?= $s->trans('custom_values_edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $urlGenerator->generate('client/edit', ['id' => $client->getClient_id()]); ?>" style="text-decoration:none">
                                    <i class="fa fa-edit fa-margin"></i> <?= $s->trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $urlGenerator->generate('quote/add'); ?>" class="client-create-quote" data-client-id="<?= $client->getClient_id(); ?>" style="text-decoration:none">
                                    <i class="fa fa-file fa-margin"></i><?= $s->trans('create_quote'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $urlGenerator->generate('inv/add'); ?>" class="client-create-invoice" style="text-decoration:none"
                                    data-client-id="<?= $client->getClient_id(); ?>">
                                    <i class="fa fa-file-text fa-margin"></i><?= $s->trans('create_invoice'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $urlGenerator->generate('client/delete',['id' => $client->getClient_id()]); ?>" style="text-decoration:none" onclick="return confirm('<?= $s->trans('delete_client_warning'); ?>');">
                                    <i class="fa fa-trash fa-margin"></i><?= $s->trans('delete'); ?>                                    
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php } ?>            
        </tbody>
    </table>
         
    <?php
        $pageSize = $paginator->getCurrentPageSize();
        if ($pageSize > 0) {
            echo Html::p(
                sprintf('Showing %s out of %s clients', $pageSize, $paginator->getTotalItems()),
                ['class' => 'text-muted']
            );
        } else {
            echo Html::p('No records');
        }
    ?>
        
</div>
</div>