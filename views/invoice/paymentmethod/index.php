<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use Yiisoft\Yii\Bootstrap5\Modal;

/**
 * @var \App\Invoice\Entity\PaymentMethod $paymentmethod
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var bool $canEdit
 * @var string $id
 * @var \Yiisoft\Session\Flash\FlashInterface $flash 
 */

?>
<h1>Payment Method</h1>
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
<div>
<?php
    if ($canEdit) {
        echo Html::a('Add',
        $urlGenerator->generate('paymentmethod/add'),
            ['class' => 'btn btn-outline-secondary btn-md-12 mb-3']
     );
    //list all the items
    foreach ($paymentmethods as $paymentmethod){
      echo Html::br();
      $label = $paymentmethod->id . " ";
      echo Html::label($label);
      echo Html::a('Edit',
      $urlGenerator->generate('paymentmethod/edit', ['id' => $paymentmethod->id]),
            ['class' => 'btn btn-info btn-sm ms-2']
          );
      echo Html::a('View',
      $urlGenerator->generate('paymentmethod/view', ['id' => $paymentmethod->id]),
      ['class' => 'btn btn-warning btn-sm ms-2']
             );
      //modal delete button
      echo Modal::widget()
      ->title('Please confirm that you want to delete this record# '.$paymentmethod->id)
      ->titleOptions(['class' => 'text-center'])
      ->options(['class' => 'testMe'])
      ->size(Modal::SIZE_SMALL)
      ->headerOptions(['class' => 'text-danger'])
      ->bodyOptions(['class' => 'modal-body', 'style' => 'text-align:center;',])
      ->footerOptions(['class' => 'text-dark'])
      ->footer(
                  Html::button(
                  'Close',
                  [
                              'type' => 'button',
                              'class' => ['btn btn-success btn-sm ms-2'],
                              'data' => [
                              'bs-dismiss' => 'modal',
                   ],
                   ]
                   ).                   Html::a('Yes Delete it Please ... I am sure!',
                   $urlGenerator->generate('paymentmethod/delete', ['id' => $paymentmethod->id]),
                   ['class' => 'btn btn-danger btn-sm ms-2']
                              )
                        )
      ->withoutCloseButton()
      ->toggleButton([
                      'class' => ['btn btn-danger btn-sm ms-2'],
                      'label' => 'Delete',
                      ])
      ->begin();
      echo '<p>Are you sure you want to delete this record? </p>';
      echo Modal::end();
      echo Html::br();
    }
    }
?>
</div>