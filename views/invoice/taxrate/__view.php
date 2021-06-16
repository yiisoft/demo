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
        <label for="tax_rate_name" class="form-label" style="background:lightblue">Tax Rate Name</label>
        <?= Html::encode($body['tax_rate_name'] ?? '') ?>
    </div>
    <div class="mb-3 form-group">
        <label for="tax_rate_percent" class="form-label">Tax Rate Percent</label>
        <?= $body['tax_rate_percent']; ?>
    </div>  
  </div> 

