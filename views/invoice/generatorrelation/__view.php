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
        <label for="lowercasename" class="form-label" style="background:lightblue">Relation Lowercase Name</label>
        <?= Html::encode($body['lowercasename'] ?? '') ?>
    </div>
    <div class="mb-3 form-group">
        <label for="camelcasename" class="form-label" style="background:lightblue">Relation Camelcase Name</label>
        <?= Html::encode($body['camelcasename'] ?? '') ?>
    </div>
    <div class="mb-3 form-group">
        <label for="view_field_name" class="form-label" style="background:lightblue">View Field Name appearing in _form and _view code</label>
        <?= Html::encode($body['view_field_name'] ?? '') ?>
    </div>
    <div class="mb-3 form-group">
        <label for="gentor_id" class="form-label" style="background:lightblue">Is a foreign key in Entity Table:</label>
        <?= $egrs->getGentor()->pre_entity_table; ?>
    </div>
  </div> 

