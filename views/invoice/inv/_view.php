<?php

declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
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

?>
<h1><?= Html::encode($title) ?></h1>
<div class="row">
 <div class="mb3 form-group">
   <label for="password" class="form-label" style="background:lightblue"><?= $s->trans('password'); ?></label>
   <?= Html::encode($body['password'] ?? ''); ?>
 </div>
 <div class="mb-3 form-group has-feedback">
        <label for="date_created" class="form-label" style="background:lightblue"><?= $s->trans('date_created'); ?></label>
        <?php
            $date_created = $body['date_created'];
            if ($date_created && $date_created != "0000-00-00") {
                //use the DateHelper
                $datehelper = new DateHelper($s);
                $date_created = $datehelper->date_from_mysql($date_created);
            } else {
                $date_created = null;
            }
        ?>      
        <?= Html::encode($date_created); ?>        
 </div>
 <div class="mb3 form-group">
   <label for="date_due" class="form-label" style="background:lightblue"><?= $s->trans('due_date'); ?></label>
        <?php
            $date_due = $body['date_due'];
            if ($date_due && $date_due != "0000-00-00") {
                //use the DateHelper
                $datehelper = new DateHelper($s);
                $date_due = $datehelper->date_from_mysql($date_due);
            } else {
                $date_due = null;
            }
        ?>      
        <?= Html::encode($date_due); ?>
 </div>
 <div class="mb3 form-group">
   <label for="terms" class="form-label" style="background:lightblue"><?= $s->trans('terms'); ?></label>
   <?= Html::encode($body['terms'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="payment_method" class="form-label" style="background:lightblue"><?= $s->trans('payment_method'); ?></label>
   <?= Html::encode($body['payment_method'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="group_id" class="form-label" style="background:lightblue"><?= $s->trans('invoice_group'); ?></label>
   <?= $inv->getGroup()->getName();?>
 </div>
 <div class="mb3 form-group">
   <label for="client_id" class="form-label" style="background:lightblue"><?= $s->trans('client'); ?></label>
   <?= $inv->getClient()->getClient_name();?>
 </div>
</div>
