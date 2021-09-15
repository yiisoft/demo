<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use App\Widget\OffsetPagination;

/**
 * @var \App\Invoice\Entity\Quote $quote
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Session\Flash\FlashInterface $flash 
 */

?>
<h1>Quote</h1>

<?php
$pagination = OffsetPagination::widget()->paginator($paginator)->urlGenerator(fn ($page) => $urlGenerator->generate('quote/index', ['page' => $page]));

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

<?php
                if ($pagination->isRequired()) {
                   echo $pagination;
                }

?>
 
                
<div class="table-responsive">
<table class="table table-hover table-striped">
   <thead>
    <tr>
                
        <th><?= $s->trans('date_created'); ?></th>
        <th><?= $s->trans('date_modified'); ?></th>
        <th><?= $s->trans('date_expires'); ?></th>
        <th><?= $s->trans('number'); ?></th>
        <th><?= $s->trans('discount_amount'); ?></th>
        <th><?= $s->trans('discount_percent'); ?></th>
        <th><?= $s->trans('url_key'); ?></th>
        <th><?= $s->trans('password'); ?></th>
        <th><?= $s->trans('notes'); ?></th>
                
        <th><?= $s->trans('inv'); ?></th>
        <th><?= $s->trans('client'); ?></th>
        <th><?= $s->trans('group'); ?></th>
        <th><?= $s->trans('user'); ?></th>

        <th><?= $s->trans('options'); ?></th>
    </tr>
   </thead>
<tbody>

<?php foreach ($paginator->read() as $quote) { ?>
     <tr>
                
      <td><?= Html::encode($quote->getDate_created()); ?></td>
      <td><?= Html::encode($quote->getDate_modified()); ?></td>
      <td><?= Html::encode($quote->getDate_expires()); ?></td>
      <td><?= Html::encode($quote->getNumber()); ?></td>
      <td><?= Html::encode($quote->getDiscount_amount()); ?></td>
      <td><?= Html::encode($quote->getDiscount_percent()); ?></td>
      <td><?= Html::encode($quote->getUrl_key()); ?></td>
      <td><?= Html::encode($quote->getPassword()); ?></td>
      <td><?= Html::encode($quote->getNotes()); ?></td>
                
        <td><?= Html::encode($quote->getInv()->id); ?></td>
        <td><?= Html::encode($quote->getClient()->id); ?></td>
        <td><?= Html::encode($quote->getGroup()->id); ?></td>
        <td><?= Html::encode($quote->getUser()->id); ?></td>

        <td>
          <div class="options btn-group">
          <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-cog"></i>
                <?= $s->trans('options'); ?>
          </a>
          <ul class="dropdown-menu">
              <li>
                  <a href="<?= $urlGenerator->generate('quote/edit',['quote_id'=>$quote->id]); ?>">                       <i class="fa fa-edit fa-margin"></i>
                       <?= $s->trans('edit'); ?>
                  </a>
              </li>
              <li>
                  <form action="<?= $urlGenerator->generate('quote/delete',['quote_id'=>$product->id]); ?>"  method="POST">
                      <?php $csrf; ?>
                      <button type="submit" class="dropdown-button" onclick="return confirm('<?= $s->trans('delete_record_warning');?>');">
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
        sprintf('Showing %s out of %s quotes', $pageSize, $paginator->getTotalItems()),
        ['class' => 'text-muted']
    );
    } else {
      echo Html::p('No records');
    }
?>
</div>
</div>
