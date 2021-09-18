<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use App\Widget\OffsetPagination;
use App\Invoice\Helpers\DateHelper;

/**
 * @var \App\Invoice\Entity\Quote $quote
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Session\Flash\FlashInterface $flash 
 */

?>
<div>
    <h5><?= $s->trans('quote'); ?></h5>
    <div class="btn-group">
        <a class="btn btn-success" href="<?= $urlGenerator->generate('quote/add'); ?>">
            <i class="fa fa-plus"></i> <?= $s->trans('new'); ?>
        </a>
    </div>
</div>
<div>
<br>
<?php 
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
</div>
<div>
<?php
  $pagination = OffsetPagination::widget()->paginator($paginator)->urlGenerator(fn ($page) => $urlGenerator->generate('quote/index', ['page' => $page]));
?>

<?php
    if ($pagination->isRequired()) {
       echo $pagination;
    }
?>
</div>                 
<div class="table-responsive">
<table class="table table-hover table-striped">
   <thead>
    <tr> 
        <th><?= $s->trans('status'); ?></th>        
        <th><?= $s->trans('quote'); ?></th>
        <th><?= $s->trans('date_created'); ?></th>
        <th><?= $s->trans('client'); ?></th>
        <th><?= $s->trans('options'); ?></th>
    </tr>
   </thead>
<tbody>

<?php foreach ($paginator->read() as $quote) { ?>
     <tr>
        <td><?= Html::encode($quote->getStatus_id()); ?></td> 
        <td><?= Html::encode($quote->getNumber()); ?></td>
        <?php  $date = $quote->getDate_created() ?? null; 
               if ($date && $date !== "0000-00-00") { 
                   //use the DateHelper
                   $datehelper = new DateHelper($s); 
                   $qdate = $datehelper->date_from_mysql($date); 
               } else { 
                   $qdate = null; 
               }
        ?>      
        <td><?= Html::encode($qdate); ?></td>
        
        <td><?= Html::encode($quote->getClient()->client_name); ?></td>

        <td>
          <div class="options btn-group">
          <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-cog"></i>
                <?= $s->trans('options'); ?>
          </a>
          <ul class="dropdown-menu">
              <li>
                  <a href="<?= $urlGenerator->generate('quote/view',['id'=>$quote->id]); ?>" style="text-decoration:none"><i class="fa fa-eye fa-margin"></i>
                       <?= $s->trans('view'); ?>
                  </a>
              </li>
              <li>
                  <a href="<?= $urlGenerator->generate('quote/edit',['id'=>$quote->id]); ?>" style="text-decoration:none"><i class="fa fa-edit fa-margin"></i>
                       <?= $s->trans('edit'); ?>
                  </a>
              </li>
              <li>
                  <a href="<?= $urlGenerator->generate('quote/delete',['id' => $quote->id]); ?>" style="text-decoration:none" onclick="return confirm('<?= $s->trans('delete_client_warning'); ?>');">
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
        sprintf('Showing %s out of %s quotes', $pageSize, $paginator->getTotalItems()),
        ['class' => 'text-muted']
    );
    } else {
      echo Html::p('No records');
    }
?>
</div>
