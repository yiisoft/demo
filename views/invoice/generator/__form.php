<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var array $tags
 * @var string $title
 */

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;

if (!empty($errors)) {
    foreach ($errors as $field => $error) {
        echo Alert::widget()->options(['class' => 'alert-danger'])->body(Html::encode($field . ':' . $error));
    }
}
?>

<h1><?= Html::encode($title) ?></h1>

<form id="generatorForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= $csrf ?>">
  <div class="container">
    <div class="row">
      <div class="col card mb3">
          <div class="card-header"><h5>Table</h5></div>  
          <label for="pre_entity_table" class="form-label required">Table used to generate Entity, Controller Add Edit Delete View, Repository, Service, Mapper</label> 
          <select name="pre_entity_table" id="pre_entity_table" class="form-control">
              <optgroup label="Table">
                  <?php foreach ($tables as $table): ?>
                      <option value="<?= $table->getName(); ?>"
                          <?= $s->check_select(Html::encode($body['pre_entity_table'] ?? ''), $table->getName()); ?>>
                          <?= $table->getName(); ?>
                      </option>
                  <?php endforeach; ?>
              </optgroup>
          </select>
      </div>
      <div class="col card mb3">
          <div class="card-header"><h5>Namespace Path</h5></div>
          <label for="namespace_path" class="form-label required">Namespace before Entity Path eg. App\Invoice (NOT App\Invoice\Entity) </label>
          <input type="text" class="form-control" name="namespace_path" id="namespace_path" placeholder="Namespace Path" value="<?= Html::encode($body['namespace_path'] ?? 'App\Invoice') ?>" required>
      </div>
    </div>
  </div>
  <br>
  <div class="container">
  <div class="card row mb-3">
    <div class="card-header"><h5>Controller and Repository</h5></div>    
    <div class="col mb-3">
        <label for="route_prefix" class="form-label required">Route Prefix eg. invoice in 'invoice/product' that will appear after the controller construct.</label>
        <input type="text" class="form-control" name="route_prefix" id="route_prefix" placeholder="Route Prefix" value="<?= Html::encode($body['route_prefix'] ?? 'invoice') ?>" required>
    </div>
    <div class="col mb-3">
        <label for="route_suffix" class="form-label required">Route Suffix eg. product in 'invoice/product' that will appear after the controller construct.</label>
        <input type="text" class="form-control" name="route_suffix" id="route_suffix" placeholder="Route Suffix" value="<?= Html::encode($body['route_suffix'] ?? 'product') ?>" required>
    </div>
    <div class="col mb-3">
        <label for="camelcase_capital_name" class="form-label required">Camelcase Capital Name used in Controller and Repository names eg. TaxRate. Use 'Product' if using above example of 'product'. </label>
        <input type="text" class="form-control" name="camelcase_capital_name" id="camelcase_capital_name" placeholder="Camelcase Capital Name used in Controller and Repository names eg. TaxRate" value="<?= Html::encode($body['camelcase_capital_name'] ?? '') ?>" placeholder = "Product" required>
    </div>
    <div class="col mb-3">
        <label for="small_singular_name" class="form-label required">Small <b>singular</b> name used in Controller for <b>edit</b>, and <b>view</b> controller functions. Normally the same as the Route Suffix. eg. product</label>
        <input type="text" class="form-control" name="small_singular_name" id="small_singular_name" placeholder="Small singular name used in Controller for edit, and view controller functions." value="<?= Html::encode($body['small_singular_name'] ?? '') ?>" placeholder = "product" required>
    </div>
    <div class="col mb-3">
        <label for="small_plural_name" class="form-label required">Small <b>plural</b> name  used in Controller for <b>index</b> controller function to list all entity generators. Normally the above value with an s on the end.</label>
        <input type="text" class="form-control" name="small_plural_name" id="small_plural_name" placeholder="Small Plural Name" value="<?= Html::encode($body['small_plural_name'] ?? '') ?>" placeholder = "products" required>
    </div>
    <div class="mb-3">
        <label for="flash_include" class="form-label">Include Flash Message in Add/Edit/View/Delete function in Controller</label>
        <input type="hidden" name="flash_include" value="0">
        <input type="checkbox"  name="flash_include" id="flash_include" value="1"
           <?php $s->check_select(Html::encode($body['flash_include'] ?? ''), 1, '==', true) ?>
        >
    </div>
    <div class="mb-3">
        <label for="headerline_include" class="form-label">Include Headline if Ajax required</label>
        <input type="hidden" name="headerline_include" value="0">
        <input type="checkbox" name="headerline_include" id="headerline_include" value="1"
           <?php $s->check_select(Html::encode($body['headerline_include'] ?? ''), 1, '==', true) ?>
        >             
    </div>
  </div>
  </div>
  <div class="card row mb-3">
      <div class="card-header"><h5>Path to Layout File</h5></div>  
    <div class="col mb-3 form-group">     
        <label for="controller_layout_dir" class="form-label required">Controller Layout Directory eg. dirname(dirname(__DIR__)) that appears just after controller construct. The Controller file sits in (__DIR__) and is two directories below 'src' directory which wil be used as a 'base' to append a path to Layout directory. </label>
        <input type="text" class="form-control" name="controller_layout_dir" id="controller_layout_dir" placeholder="Controller Layout Directory eg. dirname(dirname(__DIR__))" value="<?= Html::encode($body['controller_layout_dir'] ?? 'dirname(dirname(__DIR__))') ?>" required>
    </div>
    <div class="col mb-3 form-group">
        <label for="controller_layout_dir_dot_path" class="form-label required">Controller Layout Directory Dot Path eg. '/Invoice/Layout/main.php' that appears just after controller construct (exclude the apostrophe's) and is appended to the above src directory location.</label>
        <input type="text" class="form-control" name="controller_layout_dir_dot_path" id="controller_layout_dir_dot_path" placeholder="Controller Layout Directory Dot Path" value="<?= Html::encode($body['controller_layout_dir_dot_path'] ?? '/Invoice/Layout/main.php') ?>" required>
    </div>
  </div>
  <div class="card row mb-3">
      <div class="card-header"><h5>External Entity used in this Entity. The Setting Entity is a simple key => value pair id indexed database.</h5></div>  
    <div class="col mb-3 form-group">      
        <div class="mb-3">
            <label for="repo_extra_camelcase_name" class="form-label required">External Entity eg. MyEntity exclusive of path. Path built in Generator. Default: Setting</label>
            <input type="text" class="form-control" name="repo_extra_camelcase_name" id="repo_extra_camelcase_name" placeholder="Additional Repository eg. Setting Repository in addition to main repository." value="<?= Html::encode($body['repo_extra_camelcase_name'] ?? 'Setting') ?>">
        </div>
    </div>
  </div>   
  <div class="container">
  <div class="card row mb-3">
    <div class="card-header"><h5>Mapper</h5></div>   
        <div class="col mb-3">
            <label for="created_include" class="form-label">Include <b>created_at</b> field in Mapper</label>
            <input type="hidden" name="created_include" value="0">
            <input type="checkbox" name="created_include" id="created_include" value="1"
               <?php $s->check_select(Html::encode($body['created_include'] ?? ''), 1, '==', true) ?>    
            >
        </div>
        <div class="col mb-3">
            <label for="updated_include" class="form-label">Include <b>updated_at</b> field in Mapper</label>
            <input type="hidden" name="updated_include" value="0">
            <input type="checkbox" name="updated_include" id="updated_include" value="1"
               <?php $s->check_select(Html::encode($body['updated_include'] ?? ''), 1, '==', true) ?>    
            >
        </div>
        <div class="col mb-3">
            <label for="modified_include" class="form-label">Include <b>modified_at</b> field in Mapper</label>
            <input type="hidden" name="modified_include" value="0">
            <input type="checkbox" name="modified_include" id="modified_include" value="1"
               <?php $s->check_select(Html::encode($body['modified_include'] ?? ''), 1, '==', true) ?>    
            >
        </div>
        <div class="col mb-3">
            <label for="deleted_include" class="form-label">Include <b>deleted_at</b> field in Mapper</label>
            <input type="hidden" name="deleted_include" value="0">
            <input type="checkbox" name="deleted_include" id="deleted_include" value="1"
               <?php $s->check_select(Html::encode($body['deleted_include'] ?? ''), 1, '==', true) ?>    
            >
        </div>
        <div class="col mb-3">
            <label for="constrain_index_field" class="form-label">Field that is being used as an index eg. status and that can be used in a scope.</label>
            <input type="text" class="form-control" name="constrain_index_field" id="constrain_index_field" placeholder="Index field used in a scope." value="<?= Html::encode($body['constrain_index_field'] ?? '') ?>">
        </div>
    </div>
  </div>
  <div class="container">  
    <div class="card row mb-3">
        <div class="col mb-3">
            <label for="keyset_paginator_include" class="form-label">Include Keyset Paginator <a href="https://use-the-index-luke.com">https://use-the-index-luke.com</a></label>
            <input type="hidden" name="keyset_paginator_include" value="0">
            <input type="checkbox" name="keyset_paginator_include" id="keyset_paginator_include" value="1"
               <?php $s->check_select(Html::encode($body['keyset_paginator_include'] ?? ''), 1, '==', true) ?>    
            >
        </div>
        <div class="col mb-3">
                <label for="paginator_next_page_attribute" class="form-label required">Keyset Paginator next page attribute</label>
                <input type="text" class="form-control" name="paginator_next_page_attribute" id="paginator_next_page_attribute" placeholder="Paginator Next Page Attribute" value="<?= Html::encode($body['paginator_next_page_attribute'] ?? '') ?>">
        </div>
        <div class="col mb-3">
            <label for="offset_paginator_include" class="form-label">Include Offset Paginator (recommended by default) <a href="https://use-the-index-luke.com">https://use-the-index-luke.com</a></label>
            <input type="hidden" name="offset_paginator_include" value="0">
            <input type="checkbox" name="offset_paginator_include" id="offset_paginator_include" value="1"
               <?php $s->check_select(Html::encode($body['offset_paginator_include'] ?? ''), 1, '==', true) ?>    
            >
        </div>
    </div>
  </div>
  <div class="container">  
    <div class="card row mb-3">
    <div class="card-header"><h5>Filter</h5></div>   
        <div class="col mb-3">
            <label for="filter_field" class="form-label">Filter field eg. status_id or is_active; show here as 'status' or 'active' (without apostrophes) to appear in route.</label>
            <input type="text" class="form-control" name="filter_field" id="filter_field" placeholder="Filter field for offset paginator to produce filter button group." value="<?= Html::encode($body['filter_field'] ?? '') ?>">
        </div>
        <div class="col mb-3">
            <label for="filter_field_start_position" class="form-label">Filter field range start position eg. is_active's is 0 where 0 represents 'inactive', eg. status's is 1 representing 'draft'.</label>
            <input type="text" class="form-control" name="filter_field_start_position" id="filter_field_start_position" placeholder="Filter field start position" value="<?= Html::encode($body['filter_field_start_position'] ?? '') ?>">
        </div>
        <div class="col mb-3">
            <label for="filter_field_end_position" class="form-label">Filter field range end position eg. is_active's is 2 where 2 represents 'all' eg. status's is 6 where 6 represents 'cancelled'.</label>
            <input type="text" class="form-control" name="filter_field_end_position" id="filter_field_end_position" placeholder="Filter field end position" value="<?= Html::encode($body['filter_field_end_position'] ?? '') ?>">
        </div>
    </div>
  </div>  
  <button type="submit" class="btn btn-primary">Submit</button>
</form>