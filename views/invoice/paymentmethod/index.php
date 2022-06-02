<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var \App\Invoice\Entity\PaymentMethod $paymentmethod
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var bool $canEdit
 * @var string $id
 * @var \Yiisoft\Session\Flash\FlashInterface $flash 
 */

?>
<div id="headerbar">
    <h1 class="headerbar-title"><?= $s->trans('payment_methods'); ?></h1>
    <div class="headerbar-item pull-right">
        <?= Html::a($s->trans('new'),$urlGenerator->generate('paymentmethod/add'),['class' => 'btn btn-outline-secondary btn-md-12 mb-3']); ?>
    </div>
</div>

<div id="content" class="table-content">
    <?= $alert; ?>
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th><?= $s->trans('payment_method'); ?></th>
                <th><?= $s->trans('options'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($payment_methods as $payment_method) { ?>
                <tr>
                    <td><?= Html::encode($payment_method->getName()); ?></td>
                    <td>
                        <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                              <i class="fa fa-cog"></i>
                              <?= $s->trans('options'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?= $urlGenerator->generate('paymentmethod/view',['id'=>$payment_method->getId()]); ?>" style="text-decoration:none"><i class="fa fa-eye fa-margin"></i>
                                     <?= $s->trans('view'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $urlGenerator->generate('paymentmethod/edit',['id'=>$payment_method->getId()]); ?>" style="text-decoration:none"><i class="fa fa-edit fa-margin"></i>
                                     <?= $s->trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= $urlGenerator->generate('paymentmethod/delete',['id'=>$payment_method->getId()]); ?>" style="text-decoration:none" onclick="return confirm('<?= $s->trans('delete_record_warning'); ?>');">
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
    </div>
</div>
