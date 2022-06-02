<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use App\Widget\OffsetPagination;

/**
 * @var \App\Invoice\Entity\Task $task
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Session\Flash\FlashInterface $flash 
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

<div>
 <h5>Task</h5>
 <a class="btn btn-success" href="<?= $urlGenerator->generate('task/add'); ?>"><i class="fa fa-plus"></i>
   <?= $s->trans('new'); ?>
 </a>
</div>
<br>
<div class="submenu-row">
 <div class="btn-group index-options">
 </div>
</div>

<?php
    $pagination = OffsetPagination::widget()
    ->paginator($paginator)
    ->urlGenerator(fn ($page) => $urlGenerator->generate('task/index', ['page' => $page]));   
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
        <th><?= $s->trans('status'); ?></th>
        <th><?= $s->trans('name'); ?></th>
        <th><?= $s->trans('description'); ?></th>
        <th><?= $s->trans('price'); ?></th>
        <th><?= $s->trans('task_finish_date'); ?></th>
        <th><?= $s->trans('project'); ?></th>
        <th><?= $s->trans('tax_rate'); ?></th>
        <th><?= $s->trans('options'); ?></th>
    </tr>
   </thead>
<tbody>

<?php foreach ($paginator->read() as $task) { ?>
     <tr>
        <td><?= Html::encode($task->getStatus() ? $s->trans('active') : $s->trans('inactive')); ?></td>
        <td><?= Html::encode($task->getName()); ?></td>
        <td><?= Html::encode($task->getDescription()); ?></td>
        <td><?= Html::encode($task->getPrice()); ?></td>
        <td><?= Html::encode($d->getDate($task->getFinish_date() ?? null)); ?></td>        
        <td><?= Html::encode($task->getProject()->name); ?></td>
        <td><?php echo ($task->getTaxrate()->tax_rate_name) ? Html::encode($task->getTaxrate()->tax_rate_name) : trans('none'); ?></td>
        <td>
          <div class="options btn-group">
          <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-cog"></i>
                <?= $s->trans('options'); ?>
          </a>
          <ul class="dropdown-menu">
              <li>
                  <a href="<?= $urlGenerator->generate('task/edit',['id'=>$task->getId()]); ?>" style="text-decoration:none"><i class="fa fa-edit fa-margin"></i>
                       <?= $s->trans('edit'); ?>
                  </a>
              </li>
              <li>
                  <a href="<?= $urlGenerator->generate('task/view',['id'=>$task->getId()]); ?>" style="text-decoration:none"><i class="fa fa-eye fa-margin"></i>
                       <?= $s->trans('view'); ?>
                  </a>
              </li>
              <li>
                  <a href="<?= $urlGenerator->generate('task/delete',['id'=>$task->getId()]); ?>" style="text-decoration:none" onclick="return confirm('<?= $s->trans('delete_record_warning'); ?>');">
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
        sprintf('Showing %s out of %s tasks', $pageSize, $paginator->getTotalItems()),
        ['class' => 'text-muted']
    );
    } else {
      echo Html::p('No records');
    }
?>
</div>
</div>
