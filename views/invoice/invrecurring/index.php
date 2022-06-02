<?php
declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use App\Invoice\Helpers\DateHelper;
use App\Widget\OffsetPagination;

/**
 * @var \App\Invoice\Entity\InvRecurring $invrecurring
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
 <h1 class="headerbar-title"><?= $s->trans('recurring_invoices'); ?></h1>
 <a class="btn btn-success" href="<?= $urlGenerator->generate('inv/index'); ?>">
      <i class="fa fa-arrow-left"></i> <?= $s->trans('invoices'); ?> </a></div>

<?php
$pagination = OffsetPagination::widget()
->paginator($paginator)
->urlGenerator(fn ($page) => $urlGenerator->generate('invrecurring/index', ['page' => $page])); 
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
        <th><?= $s->trans('base_invoice'); ?></th>
        <th><?= $s->trans('client'); ?></th>
        <th><?= $s->trans('start_date'); ?></th>
        <th><?= $s->trans('end_date'); ?></th>
        <th><?= $s->trans('every'); ?></th>
        <th><?= $s->trans('next_date'); ?></th>
        <th><?= $s->trans('options'); ?></th>
    </tr>
   </thead>
<tbody>

<?php foreach ($paginator->read() as $invrecurring) { ?>
     <?php $datehelper = new DateHelper($s);
           $no_next = $datehelper->getYear_from_DateTime($invrecurring->getNext()) === '-0001' ? true : false;
     ?>
     <tr>
      <td>
            <span class="label
                            <?php if (($datehelper->getYear_from_DateTime($invrecurring->getNext()) === '-0001')) {
                            echo 'label-default';
                        } else {
                            echo 'label-success';
                        } ?>">
                            <?= $no_next ? $s->trans('inactive') : $s->trans('active') ?>
            </span>
      </td>      
      <td><a href="<?= $urlGenerator->generate('inv/view',['id'=>$invrecurring->getInv_id()]); ?>"  title="<?= $s->trans('edit'); ?>" style="text-decoration:none"><?php echo($invrecurring->getInv()->getNumber() ? $invrecurring->getInv()->getNumber() : $invrecurring->getInv_id()); ?></a></td>   
      <td><?= Html::a($invrecurring->getInv()->getClient()->getClient_name(),$urlGenerator->generate('client/view',['id'=>$invrecurring->getInv()->getClient()->getClient_id()])); ?></td>         
      <td><?= Html::encode($datehelper->date_from_mysql($invrecurring->getStart())); ?></td>
      <td><?= Html::encode($datehelper->date_from_mysql($invrecurring->getEnd())); ?></td>
      <td><?= Html::encode($s->trans($recur_frequencies[$invrecurring->getFrequency()])); ?></td>
      <!-- mySql date 0000-00-00's year is represented as -0001 in DateTime format  -->
      <td><?= Html::encode($no_next ? '' : $datehelper->date_from_mysql($invrecurring->getNext())); ?></td>
      <td>
          <div class="options btn-group">
          <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-cog"></i>
                <?= $s->trans('options'); ?>
          </a>
          <ul class="dropdown-menu">
              <li>
                <?php if (!$no_next) { ?>  
                  <a href="<?= $urlGenerator->generate('invrecurring/stop',['id'=>$invrecurring->getId()]); ?>" style="text-decoration:none"                    
                  ><i class="fa fa-edit fa-margin"></i>
                       <?= $s->trans('stop'); ?>
                  </a>
                <?php } ?>  
              </li>
              <li>
                  <a href="<?= $urlGenerator->generate('invrecurring/delete',['id'=>$invrecurring->getId()]); ?>" style="text-decoration:none">                       <i class="fa fa-trash fa-margin"></i>
                       <?= $s->trans('delete'); ?>
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
        sprintf('Showing %s out of %s invrecurrings', $pageSize, $paginator->getTotalItems()),
        ['class' => 'text-muted']
    );
    } else {
      echo Html::p('No records');
    }
?>
</div>
</div>
