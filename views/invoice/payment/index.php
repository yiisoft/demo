<?php
declare(strict_types=1);

use Yiisoft\Html\Html;
use App\Widget\OffsetPagination;

/**
 * @var \App\Invoice\Entity\Payment $payment
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Session\Flash\FlashInterface $flash 
 */
 

?>
<div>
 <h5><?= $s->trans('payment'); ?></h5>
 <a class="btn btn-success" href="<?= $urlGenerator->generate('payment/add'); ?>">
      <i class="fa fa-plus"></i> <?= $s->trans('new'); ?> </a></div>

<?php
$pagination = OffsetPagination::widget()
->paginator($paginator)
->urlGenerator(fn ($page) => $urlGenerator->generate('payment/index', ['page' => $page]));
?>

<?php
    if ($pagination->isRequired()) {
       echo $pagination;
    }
?> 


                
<div class="table-responsive">
<br>
<?php echo $alert; ?>    
    
<table class="table table-hover table-striped">
   <thead>
    <tr>       
        <th><?= $s->trans('date'); ?></th>
        <th><?= $s->trans('amount'); ?></th>
        <th><?= $s->trans('note'); ?></th>
        <th><?= $s->trans('invoice'); ?></th>
        <th><?= $s->trans('payment_method'); ?></th>
        <th><?= $s->trans('options'); ?></th>
    </tr>
   </thead>
<tbody>

<?php foreach ($paginator->read() as $payment) { ?> 
      <tr>
      <td><?= Html::encode($d->getDate($payment->getPayment_date() ?? null)); ?></td>
      <td><?= Html::encode($s->format_currency(null!==$payment->getAmount() ? $payment->getAmount() : 0.00)); ?></td>
      <td><?= Html::encode($payment->getNote()); ?></td>
      <td><?= Html::encode($payment->getInv()->getNumber() ? $payment->getInv()->getNumber() : $payment->getInv()->getId()); ?></td>
      <td><?= Html::encode(ucfirst($payment->getPaymentMethod()->getName())); ?></td>
      <td>
        <div class="options btn-group">
        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-cog" style="text-decoration: none"></i>
              <?= $s->trans('options'); ?>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a href="<?= $urlGenerator->generate('payment/edit',['id'=>$payment->getId()]); ?>" style="text-decoration: none"><i class="fa fa-edit fa-margin"></i>
                     <?= $s->trans('edit'); ?>
                </a>
            </li>
            <li>
                <form action="<?= $urlGenerator->generate('payment/delete',['id'=>$payment->getId()]); ?>">
                    <?php $csrf; ?>
                    <button type="submit" class="dropdown-button" onclick="return confirm('<?= $s->trans('delete_record_warning');?>');" style="text-decoration: none">
                     <i class="fa fa-trash fa-margin"></i>
                     <?= $s->trans('delete'); ?>
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
        sprintf('Showing %s out of %s payments', $pageSize, $paginator->getTotalItems()),
        ['class' => 'text-muted']
    );
    } else {
      echo Html::p('No records');
    }
?>
</div>
</div>
