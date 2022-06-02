<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use App\Widget\FlashMessage;
use App\Widget\OffsetPagination;
use App\Invoice\Helpers\DateHelper;
use App\Invoice\Helpers\ModalHelper;

/**
 * @var \App\Invoice\Entity\Quote $quote
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Session\Flash\FlashInterface $flash_interface
 */

$modalhelper = new ModalHelper($s);  

?>
<div>
    <h5><?= $s->trans('quote'); ?></h5>
    <div class="btn-group">
        <?php
            echo $modal_create_quote;
        ?>
        <?php if ($client_count === 0) { ?>
        <a href="#create-quote" class="btn btn-success" data-toggle="modal" disabled data-toggle="tooltip" title="<?= $s->trans('add_client'); ?>">
            <i class="fa fa-plus"></i><?= $s->trans('new'); ?>
        </a>
        <?php } else { ?>
        <a href="#create-quote" class="btn btn-success" data-toggle="modal">
            <i class="fa fa-plus"></i><?= $s->trans('new'); ?>
        </a>
        <?php } ?>
    </div>
    <br>
    <br>
    <div class="submenu-row">
            <div class="btn-group index-options">
                <a href="<?= $urlGenerator->generate('quote/index',['page'=>1,'status'=>0]); ?>"
                   class="btn <?php echo $status == 0 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('all'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('quote/index',['page'=>1,'status'=>1]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $status == 1 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('draft'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('quote/index',['page'=>1,'status'=>2]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $status == 2 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('sent'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('quote/index',['page'=>1,'status'=>3]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $status == 3 ? 'btn-primary' : 'btn-default'  ?>">
                    <?= $s->trans('viewed'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('quote/index',['page'=>1,'status'=>4]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $status == 4 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('approved'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('quote/index',['page'=>1,'status'=>5]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $status == 5 ? 'btn-primary' : 'btn-default'  ?>">
                    <?= $s->trans('rejected'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('quote/index',['page'=>1,'status'=>6]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $status == 6 ? 'btn-primary' : 'btn-default'  ?>">
                    <?= $s->trans('cancelled'); ?>
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
  $pagination = OffsetPagination::widget()->paginator($paginator)->urlGenerator(fn ($page) => $urlGenerator->generate('quote/index', ['page' => $page, 'status'=>$status]));
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
        <th><?= $s->trans('created'); ?></th>
        <th><?= $s->trans('due_date'); ?></th>
        <th><?= $s->trans('client'); ?></th>
        <th><?= $s->trans('total'); ?></th>
        <th><?= $s->trans('options'); ?></th>
    </tr>
   </thead>
<tbody>
    
    

<?php foreach ($paginator->read() as $quote) { ?>
     <tr>
        <td>
            <span <?php echo $quote->getStatus($quote->getStatus_id()); ?>>
               <?php echo ucfirst($quote->getStatus($quote->getStatus_id())); ?>
            </span>
        </td> 
       <td><a href="<?= $urlGenerator->generate('quote/view',['id'=>$quote->getId()]); ?>"  title="<?= $s->trans('edit'); ?>" style="text-decoration:none"><?php echo($quote->getNumber() ? $quote->getNumber() : $quote->getId()); ?></a></td>
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
        
        <?php  $date_due = $quote->getDate_expires() ?? null; 
               if ($date_due && $date_due !== "0000-00-00") { 
                   //use the DateHelper
                   $datehelper = new DateHelper($s); 
                   $qdate = $datehelper->date_from_mysql($date_due); 
               } else { 
                   $qdate = null; 
               }
        ?>      
        <td><?= Html::encode($qdate); ?></td>
        
        <td><?= Html::encode($quote->getClient()->getClient_name()); ?></td>
        
        <?php $quote_amount = (($qaR->repoQuoteAmountCount($quote->getId()) > 0) ? $qaR->repoQuotequery($quote->getId()) : null);?>        
        <td>
            <?php                
                echo $s->format_currency(null!==$quote_amount ? $quote_amount->getTotal() : 0.00); 
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
                  <a href="<?= $urlGenerator->generate('quote/view',['id'=>$quote->getId()]); ?>" style="text-decoration:none"><i class="fa fa-eye fa-margin"></i>
                       <?= $s->trans('view'); ?>
                  </a>
              </li>
              <li>
                  <a href="<?= $urlGenerator->generate('quote/edit',['id'=>$quote->getId()]); ?>" style="text-decoration:none"><i class="fa fa-edit fa-margin"></i>
                       <?= $s->trans('edit'); ?>
                  </a>
              </li>
              <li>
                  <a href="<?= $urlGenerator->generate('quote/delete',['id'=>$quote->getId()]); ?>" style="text-decoration:none" onclick="return confirm('<?= $s->trans('delete_record_warning'); ?>');">
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
        sprintf('Showing %s out of %s quotes: Max '. $max . ' quotes per page: Total Quotes '.$paginator->getTotalItems() , $pageSize, $paginator->getTotalItems()),
        ['class' => 'text-muted']
    );
    } else {
      echo Html::p('No records');
    }
?>
</div>

