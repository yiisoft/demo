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

<form id="generatorrelationForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
  <div class="row">
    <div class="mb-3 form-group">
     <label for="gentor_id">Entity Generator<span style="color:red">*</span></label>   
     <select name="gentor_id" id="generator_id" class="form-control simple-select" required>
                                <option value="0">Entity Generator</option>
                                <?php foreach ($generators as $generator) { ?>
                                    <option value="<?= $generator->id; ?>"
                                        <?php $s->check_select(Html::encode($body['gentor_id'] ?? ''), $generator->id); ?>
                                    ><?= $generator->camelcase_capital_name; ?></option>
                                <?php } ?>
     </select>
    </div>   
    <div class="mb-3 form-group">
        <label for="lowercasename">Lowercase name excluding id (eg. tax_rate_id 'foreign key/relation' in Product table simplified to tax_rate) <span style="color:red">*</span></label>
        <input type="text" class="form-control" name="lowercasename" id="lowercasename" placeholder="Entity Generator Relation BelongsTo Lowercase Name eg. tax_rate" value="<?= Html::encode($body['lowercasename'] ?? '') ?>" required>
        <label for="camelcasename">Camelcase name excluding id (eg. tax_rate_id 'foreign key/relation' in Product table simplified to TaxRate)<span style="color:red">*</span></label>
        <input type="text" class="form-control" name="camelcasename" id="camelcasename" placeholder="Entity Generator Relation Camelcase Name eg. TaxRate" value="<?= Html::encode($body['camelcasename'] ?? '') ?>" required>
        <label for="view_field_name">View Field Name<span style="color:red">*</span></label>
        <input type="text" class="form-control" name="view_field_name" id="view_field_name" placeholder="Table View Field Name that will be used in Generator's Table's _form dropdown box and _view eg. id, name" value="<?= Html::encode($body['view_field_name'] ?? '') ?>" required>
    </div>     
  </div>    
  <button type="submit" class="btn btn-primary"><?= $s->trans('submit'); ?></button>
</form>