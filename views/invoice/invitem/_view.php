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
   <label for="id" class="form-label" style="background:lightblue"><?= $s->trans('id'); ?></label>
   <?= Html::encode($body['id'] ?? ''); ?>
 </div>
 <div class="mb-3 form-group has-feedback">
        <label for="date_added" class="form-label" style="background:lightblue"><?= $s->trans('date_created'); ?></label>
        <?php
            $date_added = $body['date_added'] ?? null;
            if ($date_added && $date_added != "0000-00-00") {
                //use the DateHelper
                $datehelper = new DateHelper($s);
                $date_added = $datehelper->date_from_mysql($date_added);
            } else {
                $date_added = null;
            }
        ?>      
        <?= Html::encode($date_added); ?>        
 </div>  
 <div class="mb3 form-group">
   <label for="name" class="form-label" style="background:lightblue"><?= $s->trans('name'); ?></label>
   <?= Html::encode($body['name'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="description" class="form-label" style="background:lightblue"><?= $s->trans('description'); ?></label>
   <?= Html::encode($body['description'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="quantity" class="form-label" style="background:lightblue"><?= $s->trans('quantity'); ?></label>
   <?= Html::encode($body['quantity'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="price" class="form-label" style="background:lightblue"><?= $s->trans('price'); ?></label>
   <?= Html::encode($body['price'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="discount_amount" class="form-label" style="background:lightblue"><?= $s->trans('item_discount'); ?></label>
   <?= Html::encode($body['discount_amount'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="order" class="form-label" style="background:lightblue"><?= $s->trans('order'); ?></label>
   <?= Html::encode($body['order'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="product_unit" class="form-label" style="background:lightblue"><?= $s->trans('unit'); ?></label>
   <?= Html::encode($body['product_unit'] ?? ''); ?>
 </div>
 <div class="mb-3 form-group has-feedback">
        <label for="date" class="form-label" style="background:lightblue"><?= $s->trans('item_date'); ?></label>
        <?php
            $date = $body['date'] ?? null;
            if ($date && $date != "0000-00-00") {
                //use the DateHelper
                $datehelper = new DateHelper($s);
                $date = $datehelper->date_from_mysql($date);
            } else {
                $date = null;
            }
        ?>      
        <?= Html::encode($date); ?>        
 </div>  
 <div class="mb3 form-group">
   <label for="inv_id" class="form-label" style="background:lightblue"><?= $s->trans('invoice'); ?></label>
   <?= $item->getInv()->getId();?>
 </div>
 <div class="mb3 form-group">
   <label for="tax_rate_id" class="form-label" style="background:lightblue"><?= $s->trans('tax_rate'); ?></label>
   <?= $item->getTaxRate()->tax_rate_name;?>
 </div>
 <div class="mb3 form-group">
   <label for="product_id" class="form-label" style="background:lightblue"><?= $s->trans('product'); ?></label>
   <?= $item->getProduct()->product_name;?>
 </div>
 <div class="mb3 form-group">
   <label for="unit_id" class="form-label" style="background:lightblue"><?= $s->trans('unit'); ?></label>
   <?= $item->getUnit()->unit_name;?>
 </div>
 <div class="mb3 form-group">
   <label for="task_id" class="form-label" style="background:lightblue">Task</label>
   <?= $item->getTask()->task_name;?>
 </div>
</div>
