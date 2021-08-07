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
 <div class="mb3 form-group">
<label for="fieldid" class="form-label" style="background:lightblue">Field Id</label>
   <?= Html::encode($body['fieldid'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
<label for="fieldvalue" class="form-label" style="background:lightblue">Field Value</label>
   <?= Html::encode($body['fieldvalue'] ?? ''); ?>
 </div>
 <div class="mb3 form-group">
   <label for="inv_id" class="form-label" style="background:lightblue">Invoice Id</label>
   <?= $invcust->getInv()->id;?>
 </div>
</div>
