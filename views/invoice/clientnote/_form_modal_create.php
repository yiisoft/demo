<?php

declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use Yiisoft\Yii\Bootstrap5\Modal;
use App\Invoice\Helpers\DateHelper;

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var string $title
 */

if (!empty($errors)) {
    foreach ($errors as $field => $error) {
        echo Alert::widget()->options(['class' => 'alert-danger'])->body(Html::encode($field . ':' . $error));
    }
}

      echo Modal::widget()
      ->title('Clientnote Form')
      ->titleOptions(['class' => 'text-center'])
      ->options(['class' => 'testMe'])
      ->size(Modal::SIZE_LARGE)
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
                   ).                   Html::a('Submit the Details.',
                   ['class' => 'btn btn-danger btn-sm ms-2']
                              )
                        )
      ->withoutCloseButton()
      ->toggleButton([
                      'class' => ['btn btn-danger btn-sm ms-2'],
                      'label' => 'Submit',
                      ])
      ->begin();
      echo '<p> </p>';
      $response = $head->renderPartial('invoice/views/clientnote/_form_modal_field');
      echo (string)$response->getBody();      echo Modal::end();
      echo Html::br();
?>
</div>