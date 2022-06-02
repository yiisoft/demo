<?php
declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 */
?>

<h1><?= Html::encode($body['title']) ?></h1>
  <div class="row">
    <div class="mb-3 form-group">
        <label for="family_name" name="family_name" id="family_name" class="form-label" style="background:lightblue" value="<?= Html::encode($body['family_name'] ?? '') ?>">Family Name</label>
        <?= Html::encode($body['family_name'] ?? '') ?>
    </div>
    <div class="mb-3 form-group">
        <label for="id" name="id" id="id" class="form-label" style="background:lightblue" value="<?= Html::encode($body['id'] ?? '') ?>">Family Id</label>
        <?= Html::encode($body['id'] ?? '') ?>
    </div>  
  </div>
<button name="xyz" id="xyz">xyz</button>
