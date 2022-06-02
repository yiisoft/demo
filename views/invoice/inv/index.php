<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use App\Widget\OffsetPagination;
use App\Invoice\Helpers\DateHelper;
use App\Invoice\Helpers\ModalHelper;

/**
 * @var \App\Invoice\Entity\Inv $inv
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Session\Flash\FlashInterface $flash_interface
 */

$modalhelper = new ModalHelper($s);  

?>
<div>
    <h5><?= $s->trans('invoice'); ?></h5>
    <div class="btn-group">
        <?php
            echo $modal_create_inv;
        ?>
        <?php if ($client_count === 0) { ?>
        <a href="#create-inv" class="btn btn-success" data-toggle="modal" disabled data-toggle="tooltip" title="<?= $s->trans('add_client'); ?>">
            <i class="fa fa-plus"></i><?= $s->trans('new'); ?>
        </a>
        <?php } else { ?>
        <a href="#create-inv" class="btn btn-success" data-toggle="modal">
            <i class="fa fa-plus"></i><?= $s->trans('new'); ?>
        </a>
        <?php } ?>
    </div>
    <br>
    <br>
    <div class="submenu-row">
            <div class="btn-group index-options">
                <a href="<?= $urlGenerator->generate('inv/index',['page'=>1,'status'=>0]); ?>"
                   class="btn <?php echo $status == 0 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('all'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('inv/index',['page'=>1,'status'=>1]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $status == 1 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('draft'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('inv/index',['page'=>1,'status'=>2]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $status == 2 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('sent'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('inv/index',['page'=>1,'status'=>3]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $status == 3 ? 'btn-primary' : 'btn-default'  ?>">
                    <?= $s->trans('viewed'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('inv/index',['page'=>1,'status'=>4]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $status == 4 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('paid'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('inv/index',['page'=>1,'status'=>5]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $status == 5 ? 'btn-primary' : 'btn-default'  ?>">
                    <?= $s->trans('overdue'); ?>
                </a>
            </div>
    </div>
</div>
<div>
<br>
<?= $alert; ?>    
</div>
<div>
<?php
  $pagination = OffsetPagination::widget()->paginator($paginator)->urlGenerator(fn ($page) => $urlGenerator->generate('inv/index', ['page' => $page, 'status'=>$status]));
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
        <th><?= $s->trans('invoice'); ?></th>
        <th><?= $s->trans('created'); ?></th>
        <th><?= Html::tag('i','',['class'=>'fa fa-clock-o']); ?></th>
        <th><?= $s->trans('due_date'); ?></th>
        <th><?= $s->trans('client'); ?></th>
        <th style="text-align: right;"><?= $s->trans('amount'); ?></th>
        <th style="text-align: right;"><?= $s->trans('paid'); ?></th>
        <th style="text-align: right;"><?= $s->trans('balance'); ?></th>
        <th><?= $s->trans('options'); ?></th>
    </tr>
   </thead>
<tbody>

<?php foreach ($paginator->read() as $inv) { ?>
     <tr>
        <td>
            <span class="label <?= $inv_statuses[$inv->getStatus_id()]['class']; ?>">
                <?php echo $inv_statuses[$inv->getStatus_id()]['label'];
                if ($inv->getCreditinvoice_parent_id()>0) { ?>
                    &nbsp;<i class="fa fa-credit-invoice" title="<?= $s->trans('credit_invoice') ?>"></i>
                <?php } ?>
                <?php if ($inv->getIs_read_only()) { ?>
                    &nbsp;<i class="fa fa-read-only" title="<?= $s->trans('read_only') ?>"></i>
                <?php } ?>
                <?php if ($irR->repoCount((string)$inv->getId()) > 0) { ?>
                    &nbsp;<i class="fa fa-refresh" title="<?= $s->trans('recurring') ?>"></i>
                <?php } ?>
            </span>
       </td> 
       <td><a href="<?= $urlGenerator->generate('inv/view',['id'=>$inv->getId()]); ?>"  title="<?= $s->trans('edit'); ?>" style="text-decoration:none"><?php echo($inv->getNumber() ? $inv->getNumber() : $inv->getId()); ?></a></td>
        <?php  $date = $inv->getDate_created() ?? null; 
               if ($date && $date !== "0000-00-00") { 
                   //use the DateHelper
                   $datehelper = new DateHelper($s); 
                   $idate = $datehelper->date_from_mysql($date); 
               } else { 
                   $idate = null; 
               }
        ?>
       <td><?= Html::encode($idate); ?></td>
        <?php  $time = $inv->getTime_created() ?? null; 
               if ($time && $time !== "00:00:00") { 
                   //use the DateHelper
                   $datehelper = new DateHelper($s); 
                   $itime = $datehelper->getTime_from_datetime($time); 
               } else { 
                   $itime = null; 
               }
        ?>      
        <td><?= Html::encode($itime); ?></td>
        
        <?php  $date_due = $inv->getDate_due() ?? null; 
               if ($date_due && $date_due !== "0000-00-00") { 
                   //use the DateHelper
                   $datehelper = new DateHelper($s); 
                   $due = $datehelper->date_from_mysql($date_due); 
               } else { 
                   $due = null; 
               }
        ?>      
        <td><?= Html::encode($due); ?></td>
        
        <td><?= Html::encode($inv->getClient()->getClient_name()); ?></td>
        <?php $inv_amount = (($iaR->repoInvAmountCount((string)$inv->getId()) > 0) ? $iaR->repoInvquery((string)$inv->getId()) : null);?>        
        <td class="amount <?php if ((null!==$inv_amount) && ($inv_amount->getSign() == '-1')) {
            echo 'text-danger';} ?>">
            <?php                
                echo $s->format_currency(null!==$inv_amount ? $inv_amount->getTotal() : 0.00); 
            ?>
        </td>
        
        <td class="amount">
            <?php                
                echo $s->format_currency(null!==$inv_amount ? $inv_amount->getPaid() : 0.00); 
            ?>
        </td>

        <td class="amount">
            <?php                
                echo $s->format_currency(null!==$inv_amount ? $inv_amount->getBalance() : 0.00); 
            ?>
        </td>

        <td>
          <div class="options btn-group">
          <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-cog"></i>
                <?= $s->trans('options'); ?>
          </a>
          <ul class="dropdown-menu">
              <li>
                  <a href="<?= $urlGenerator->generate('inv/view',['id'=>$inv->getId()]); ?>" style="text-decoration:none"><i class="fa fa-eye fa-margin"></i>
                       <?= $s->trans('view'); ?>
                  </a>
              </li>
              <?php if ($inv->getIs_read_only() != 1) { ?>
              <li>
                  <a href="<?= $urlGenerator->generate('inv/edit',['id'=>$inv->getId()]); ?>" style="text-decoration:none"><i class="fa fa-edit fa-margin"></i>
                       <?= $s->trans('edit'); ?>
                  </a>
              </li>
              <?php } ?>
              <li>
                  <a href="<?= $urlGenerator->generate('inv/delete',['id'=>$inv->getId()]); ?>" style="text-decoration:none" onclick="return confirm('<?= $s->trans('delete_record_warning'); ?>');">
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
        sprintf('Showing %s out of %s invoices: Max '. $max . ' invoices per page: Total Invs '.$paginator->getTotalItems() , $pageSize, $paginator->getTotalItems()),
        ['class' => 'text-muted']
    );
    } else {
      echo Html::p('No records');
    }
?>
</div>

