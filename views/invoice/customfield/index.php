<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use App\Widget\OffsetPagination;

/**
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 */
 
 $danger = $flash->get('danger');
        if ($danger != null) {
            $alert =  Alert::widget()
            ->body($danger)
            ->options(['class' => ['alert-danger shadow'],])
            ->render();
            echo $alert;
        }
        $info = $flash->get('info');
        if ($info != null) {
            $alert =  Alert::widget()
            ->body($info)
            ->options(['class' => ['alert-info shadow'],])
            ->render();
            echo $alert;
        }
        $warning = $flash->get('warning');
        if ($warning != null) {
            $alert =  Alert::widget()
            ->body($warning)
            ->options(['class' => ['alert-warning shadow'],])
            ->render();
            echo $alert;
        }
?>
<div id="headerbar">
    <h1 class="headerbar-title"><?= $s->trans('custom_fields'); ?></h1>

    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?= $urlGenerator->generate('customfield/add'); ?>">
            <i class="fa fa-plus"></i><?= $s->trans('new'); ?>
        </a>
    </div>
    <div class="headerbar-item pull-right">
        <?php
            $pagination = OffsetPagination::widget()
            ->paginator($paginator)
            ->urlGenerator(fn ($page) => $urlGenerator->generate('customfield/index', ['page' => $page]));
        ?>
        <?php
            if ($pagination->isRequired()) {
               echo $pagination;
            }
        ?>
    </div>
</div>          
<div class="table-responsive">
<table class="table table-hover table-striped">
   <thead>
    <tr>
        <th><?= $s->trans('table'); ?></th>
        <th><?= $s->trans('label'); ?></th>
        <th><?= $s->trans('type'); ?></th>
        <th><?= $s->trans('order'); ?></th>
        <th><?= $s->trans('options'); ?></th>
    </tr>
   </thead>
<tbody>

<?php foreach ($paginator->read() as $customfield) { ?>
     <?php $alpha = str_replace("-", "_", strtolower($customfield->getType())); ?>
     <tr>                
      <td><?= $s->lang($custom_tables[$customfield->getTable()]); ?></td>
      <td><?= Html::encode($customfield->getLabel()); ?></td>
      <td><?= $s->trans($alpha); ?></td>
      <td><?= Html::encode($customfield->getOrder()); ?></td>
      <td>
            <div class="options btn-group btn-group-sm">
                <?php if (in_array($customfield->getType(), $custom_value_fields)) { ?>
                        <a href="<?= $urlGenerator->generate('customvalue/field',['id'=>$customfield->getId()]) ?>" class="btn btn-default" style="text-decoration:none">
                        <i class="fa fa-list fa-margin"></i><?= $s->trans('values'); ?></a>
                <?php } ?>
                <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-cog"></i><?= $s->trans('options'); ?>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="<?= $urlGenerator->generate('customfield/edit',['id'=>$customfield->getId()]); ?>" style="text-decoration:none">
                            <i class="fa fa-edit fa-margin"></i> <?= $s->trans('edit'); ?>
                        </a>
                    </li>
                    <li>
                        <form action="<?= $urlGenerator->generate('customfield/delete',['id'=>$customfield->getId()]); ?>"
                              method="POST">
                            <input type="hidden" name="_csrf" value="<?= $csrf; ?>">
                            <button type="submit" class="dropdown-button" style="text-decoration:none"
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
        sprintf('Showing %s out of %s customfields. Max: '.$max. ' Total: '.$paginator->getTotalItems(), $pageSize, $paginator->getTotalItems()),
        ['class' => 'text-muted']
    );
    } else {
      echo Html::p('No records');
    }
?>
</div>
</div>

