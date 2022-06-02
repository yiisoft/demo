<?php
declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;

    $danger = $flash->get('danger');
    if ($danger != null) {
        $alert =  Alert::widget()
            ->body($danger)
            ->options([
                'class' => ['alert-danger shadow'],
            ])
        ->render();
        echo $alert;
    }
    $info = $flash->get('info');
    if ($info != null) {
        $alert =  Alert::widget()
            ->body($info)
            ->options([
                'class' => ['alert-info shadow'],
            ])
        ->render();
        echo $alert;
    }
    $warning = $flash->get('warning');
    if ($warning != null) {
        $alert =  Alert::widget()
            ->body($warning)
            ->options([
                'class' => ['alert-warning shadow'],
            ])
        ->render();
        echo $alert;
    }
    if (!empty($errors)) {
    foreach ($errors as $field => $error) {
        echo Alert::widget()->options(['class' => 'alert-danger'])->body(Html::encode($field . ':' . $error));
    }
}
