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
        <label for="family_name" class="form-label" style="background:lightblue">Family Name</label>
        <?= Html::encode($body['family_name'] ?? '') ?>
    </div>  
  </div> 

