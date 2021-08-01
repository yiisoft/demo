<?php

declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;

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
<form id="ProjectForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
<div id="headerbar">
<h1 class="headerbar-title"><?= $s->trans('projects_form'); ?></h1>
<?php $response = $head->renderPartial('invoice/layout/header_buttons',['s'=>$s, 'hide_submit_button'=>false ,'hide_cancel_button'=>false]); ?>        
<?php echo (string)$response->getBody(); ?><div id="content">
<div class="row">
 <div class="mb3 form-group">
    <label for="client_id"><?= $s->trans('client'); ?></label>
    <select name="client_id" id="client_id" class="form-control simple-select">
       <option value="0">Client</option>
         <?php foreach ($clients as $client) { ?>
          <option value="<?= $client->id; ?>"
           <?php $s->check_select(Html::encode($body['client_id'] ?? ''), $client->id) ?>
           ><?= $client->client_name; ?></option>
         <?php } ?>
    </select>
 </div>
 <div class="mb3 form-group">
   <label for="project_name"><?= $s->trans('project_name'); ?></label>
   <input type="text" name="project_name" id="project_name" class="form-control"
      value="<?= Html::encode($body['project_name'] ?? ''); ?>" required>
 </div>
</div>
</form>
