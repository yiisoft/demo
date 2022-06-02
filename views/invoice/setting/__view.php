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
        <label for="setting_id" class="form-label" style="background:lightblue">Setting Id</label>
        <?= Html::encode($body['setting_id'] ?? '') ?>
    </div>
    <div class="mb-3 form-group">
        <label for="setting_key" class="form-label" style="background:lightblue">Setting Key</label>
        <?= Html::encode($body['setting_key'] ?? '') ?>
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="setting_value" class="form-label" style="background:lightblue">Setting Value</label>
        <?= Html::encode($body['setting_value'] ?? '') ?>         
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="setting_trans" class="form-label" style="background:lightblue">Setting Translation Key</label>
        <?= Html::encode($body['setting_trans'] ?? '') ?>         
    </div>    
    <div class="mb-3 form-group no-margin">
        <label for="setting_section" class="form-label" style="background:lightblue">Setting Section</label>
        <?= Html::encode($body['setting_section'] ?? '') ?>         
    </div>        
    <div class="mb-3 form-group no-margin">
        <label for="setting_subsection" class="form-label" style="background:lightblue">Setting Subsection</label>
        <?= Html::encode($body['setting_subsection'] ?? '') ?>         
    </div>  
  </div> 

