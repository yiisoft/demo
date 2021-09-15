<?php

declare(strict_types=1);

/**
 * @var \App\Invoice\Entity\Product $product
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator 
 * @var \Yiisoft\Data\Paginator\OffsetPaginator $paginator
 * @var \Yiisoft\Session\Flash\FlashInterface $flash 
 */

use Yiisoft\Yii\Bootstrap5\Alert;
use App\Widget\OffsetPagination;
use Yiisoft\Html\Html;
?>

<div>
    <h5><?= $s->trans('products'); ?></h5>
    <div class="btn-group">
        <a class="btn btn-success" href="<?= $urlGenerator->generate('product/add'); ?>">
            <i class="fa fa-plus"></i> <?= $s->trans('new'); ?>
        </a>
    </div>
</div>

<?php
  $pagination = OffsetPagination::widget()
  ->paginator($paginator)
  ->urlGenerator(fn ($page) => $urlGenerator->generate('product/index', ['page' => $page]));
?>

<div id="content" class="table-content">
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
    <?php 
                if ($pagination->isRequired()) {
                   echo $pagination;
                }
    ?>  
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th><?= $s->trans('family'); ?></th>
                <th><?= $s->trans('product_sku'); ?></th>
                <th><?= $s->trans('product_name'); ?></th>
                <th><?= $s->trans('product_description'); ?></th>
                <th><?= $s->trans('product_price'); ?></th>
                <th><?= $s->trans('product_unit'); ?></th>
                <th><?= $s->trans('tax_rate'); ?></th>
                <?php if ($s->get_setting('sumex')) : ?>
                    <th><?= $s->trans('tariff'); ?></th>
                <?php endif; ?>
                <th><?= $s->trans('options'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($paginator->read() as $product) { ?>
                <tr>
                    <td><?= Html::encode($product->getFamily()->family_name); ?></td>
                    <td><?= Html::encode($product->product_sku); ?></td>
                    <td><?= Html::encode($product->product_name); ?></td>
                    <td><?php echo nl2br(Html::encode($product->product_description)); ?></td>
                    <td class="amount"><?php echo $s->format_currency($product->product_price); ?></td>
                    <td><?= Html::encode($product->getUnit()->unit_name); ?></td>
                    <td><?php echo ($product->getTaxrate()->id) ? Html::encode($product->getTaxrate()->tax_rate_name) : trans('none'); ?></td>
                    <?php if ($s->get_setting('sumex')) : ?>
                        <td><?= Html::encode($product->tariff); ?></td>
                    <?php endif; ?>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?= $s->trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?= $urlGenerator->generate('product/edit',['product_id'=>$product->id]); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?= $s->trans('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <form action="<?= $urlGenerator->generate('product/delete',['product_id'=>$product->id]); ?>"
                                          method="POST">
                                        <?php $csrf; ?>
                                        <button type="submit" class="dropdown-button"
                                                onclick="return confirm('<?= $s->trans('delete_record_warning'); ?>');">
                                            <i class="fa fa-trash fa-margin"></i> <?= $s->trans('delete'); ?>
                                        </button>
                                    </form>
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
                sprintf('Showing %s out of %s products', $pageSize, $paginator->getTotalItems()),
                ['class' => 'text-muted']
            );
        } else {
            echo Html::p('No records');
        }
        ?>
    </div>
</div>
