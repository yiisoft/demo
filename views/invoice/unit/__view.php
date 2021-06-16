<?php
declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var string $title
 * @var $s
 */
?>

<h1><?= Html::encode($title) ?></h1>

  <div class="row">
    <div class="mb-3 form-group">
        <label for="unit_id" class="form-label" style="background:lightblue">Unit Id</label>
        <?= Html::encode($body['unit_id'] ?? '') ?>
    </div>
    <div class="mb-3 form-group">
        <label for="unit_name" class="form-label" style="background:lightblue">Unit Name</label>
        <?= Html::encode($body['unit_name'] ?? '') ?>
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="unit_name_plrl" class="form-label" style="background:lightblue">Unit Name Plural</label>
        <?= Html::encode($body['unit_name_plrl'] ?? '') ?>         
    </div>  
  </div> 

