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
  ->urlGenerator(fn ($page) => $urlGenerator->generate('client/index', ['page' => $page]));
?>

<div>
    <h5><?= $s->trans('clients'); ?></h5>
    <div class="btn-group">
        <a class="btn btn-success" href="<?= $urlGenerator->generate('client/add'); ?>">
            <i class="fa fa-plus"></i> <?= $s->trans('new'); ?>
        </a>
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
		    <?= ($client->client_active) ? '<span class="label active">' . $s->trans('yes') . '</span>' : '<span class="label inactive">' . $s->trans('no') . '</span>'; ?>
		</td>
                <td><?= Html::a($client->client_name." ".$client->client_surname,$urlGenerator->generate('client/view',['id' => $client->id]),['class' => 'btn btn-warning ms-2']);?></td>
                <td><?= Html::encode($client->client_email); ?></td>
                <td><?= Html::encode($client->client_phone ? $client->client_phone : ($client->client_mobile ? $client->client_mobile : '')); ?></td>
                <td class="amount"><?php // $s->format_currency($client->client_invoice_balance); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#" style="text-decoration:none">
                            <i class="fa fa-cog"></i> <?= $s->trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?= $urlGenerator->generate('client/view',['id' => $client->id]); ?>" style="text-decoration:none">
                                    <i class="fa fa-eye fa-margin"></i> <?= $s->trans('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $urlGenerator->generate('client/edit', ['id' => $client->id]); ?>" style="text-decoration:none">
                                    <i class="fa fa-edit fa-margin"></i> <?= $s->trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $urlGenerator->generate('quote/add'); ?>" class="client-create-quote" data-client-id="<?= $client->id; ?>" style="text-decoration:none">
                                    <i class="fa fa-file fa-margin"></i><?= $s->trans('create_quote'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $urlGenerator->generate('inv/add'); ?>" class="client-create-invoice" style="text-decoration:none"
                                    data-client-id="<?= $client->id; ?>">
                                    <i class="fa fa-file-text fa-margin"></i><?= $s->trans('create_invoice'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $urlGenerator->generate('client/delete',['id' => $client->id]); ?>" style="text-decoration:none" onclick="return confirm('<?= $s->trans('delete_client_warning'); ?>');">
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