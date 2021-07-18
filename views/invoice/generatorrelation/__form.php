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
        <label for="lowercasename">Lowercase name<span style="color:red">*</span></label>
        <input type="text" class="form-control" name="lowercasename" id="lowercasename" placeholder="Entity Generator Relation BelongsTo Lowercase Name eg. tax_rate" value="<?= Html::encode($body['lowercasename'] ?? '') ?>" required>
        <label for="camelcasename">Camelcase name<span style="color:red">*</span></label>
        <input type="text" class="form-control" name="camelcasename" id="camelcasename" placeholder="Entity Generator Relation Camelcase Name eg. TaxRate" value="<?= Html::encode($body['camelcasename'] ?? '') ?>" required>
    </div>
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
  </div>    
  <button type="submit" class="btn btn-primary"><?= $s->trans('submit'); ?></button>
</form>