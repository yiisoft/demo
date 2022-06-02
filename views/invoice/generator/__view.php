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
 */
?>

<h1><?= Html::encode($title) ?></h1>

  <div class="row">
    <div class="mb-3 form-group">
        
    </div>      
    <div class="mb-3 form-group">
        <label for="route_suffix" class="form-label" style="background:lightblue">Route Suffix</label>
        <?= Html::encode($body['route_suffix'] ?? '') ?>
    </div>
    <div class="mb-3 form-group">
        <label for="route_prefix" class="form-label" style="background:lightblue">Route Prefix</label>
        <?= Html::encode($body['route_prefix'] ?? '') ?>
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="camelcase_capital_name" class="form-label" style="background:lightblue">Camelcase capital name</label>
        <?= Html::encode($body['camelcase_capital_name'] ?? '') ?>         
    </div>  
  </div>
  <div class="row">
    <div class="mb-3 form-group">
        <label for="small_singular_name" class="form-label" style="background:lightblue">Small Singular Name</label>
        <?= Html::encode($body['small_singular_name'] ?? '') ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="small_plural_name" class="form-label" style="background:lightblue">Small Plural Name</label>
        <?= Html::encode($body['small_plural_name'] ?? '') ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="namespace_path" class="form-label" style="background:lightblue">Namespace Path</label>
        <?= Html::encode($body['namespace_path'] ?? '') ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="controller_layout_dir" class="form-label" style="background:lightblue">Controller Layout Dir</label>
        <?= Html::encode($body['controller_layout_dir'] ?? '') ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="controller_layout_dir_dot_path" class="form-label" style="background:lightblue">Controller Layout Directory Dot Path</label>
        <?= Html::encode($body['controller_layout_dir_dot_path'] ?? '') ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="repo_extra_camelcase_name" class="form-label" style="background:lightblue">Repo extra camelcase name</label>
        <?= Html::encode($body['repo_extra_camelcase_name'] ?? '') ?>            
    </div>
  </div>
  <div class="row">
    <div class="mb-3 form-group">
        <label for="paginator_next_page_attribute" class="form-label" style="background:lightblue">Paginator Next Page Attribute</label>        
        <?= Html::encode($body['paginator_next_page_attribute'] ?? '') ?>
    </div>            
    <div class="mb-3 form-group">
        <label for="pre_entity_table" class="form-label" style="background:lightblue">Pre Entity Table</label>
        <?= Html::encode($body['pre_entity_table'] ?? '') ?>
    </div>
    <div class="mb-3 form-group">
        <label for="constrain_index_field" class="form-label" style="background:lightblue">Index field used in a scope</label>
        <?= Html::encode($body['constrain_index_field'] ?? '') ?>
    </div>   
  </div>    
  <div class="row">
    <div class="mb-3">
        <label for="created_include" class="form-label">Include Date Created Field in Mapper</label>
        <input type="hidden" name="created_include" value="0">
        <input type="checkbox" name="created_include" id="created_include" value="1"
           <?php $s->check_select(Html::encode($body['created_include'] ?? ''), 1, '==', true) ?>    
               disabled="true">
    </div>
    <div class="mb-3">
        <label for="updated_include" class="form-label">Include Date Updated Field in Mapper</label>
        <input type="hidden" name="updated_include" value="0">
        <input type="checkbox" name="updated_include" id="updated_include" value="1"
           <?php $s->check_select(Html::encode($body['updated_include'] ?? ''), 1, '==', true) ?>    
               disabled="true">
    </div>
    <div class="mb-3">
        <label for="modified_include" class="form-label">Include Date Modified Field in Mapper</label>
        <input type="hidden" name="modified_include" value="0">
        <input type="checkbox" name="modified_include" id="modified_include" value="1"
           <?php $s->check_select(Html::encode($body['modified_include'] ?? ''), 1, '==', true) ?>    
               disabled="true">
    </div>
    <div class="mb-3">
        <label for="deleted_include" class="form-label">Include Date Deleted Field in Mapper</label>
        <input type="hidden" name="deleted_include" value="0">
        <input type="checkbox" name="deleted_include" id="deleted_include" value="1"
           <?php $s->check_select(Html::encode($body['deleted_include'] ?? ''), 1, '==', true) ?>    
               disabled="true">
    </div>  
    <div class="mb-3">
        <label for="keyset_paginator_include" class="form-label">Include Keyset Paginator</label>
        <input type="hidden" name="keyset_paginator_include" value="0">
        <input type="checkbox" name="keyset_paginator_include" id="paginator_include" value="1"
           <?php $s->check_select(Html::encode($body['keyset_paginator_include'] ?? ''), 1, '==', true) ?>    
               disabled="true">
    </div>      
    <div class="mb-3">
        <label for="offset_paginator_include" class="form-label">Include Offset Paginator</label>
        <input type="hidden" name="offset_paginator_include" value="0">
        <input type="checkbox" name="offset_paginator_include" id="offset_paginator_include" value="1"
           <?php $s->check_select(Html::encode($body['offset_paginator_include'] ?? ''), 1, '==', true) ?>    
               disabled="true">
    </div>
    <div class="mb-3 form-group">
        <label for="filter_field" class="form-label" style="background:lightblue">Filter Field</label>
        <?= Html::encode($body['filter_field'] ?? '') ?>
    </div>
    <div class="mb-3 form-group">
        <label for="filter_field_start_position" class="form-label" style="background:lightblue">Filter Field Start Position</label>
        <?= Html::encode($body['filter_field_start_position'] ?? '') ?>
    </div>
    <div class="mb-3 form-group">
        <label for="filter_field_end_position" class="form-label" style="background:lightblue">Filter Field End Position</label>
        <?= Html::encode($body['filter_field_end_position'] ?? '') ?>
    </div>  
    <div class="mb-3">
        <label for="flash_include" class="form-label">Include Flash Message</label>
        <input type="hidden" name="flash_include" value="0">
        <input type="checkbox"  name="flash_include" id="flash_include" value="1"
           <?php $s->check_select(Html::encode($body['flash_include'] ?? ''), 1, '==', true) ?>
               disabled="true">
    </div>
    
    <div class="mb-3">
        <label for="headerline_include" class="form-label">Include Headline if Ajax required</label>
        <input type="hidden" name="headerline_include" value="0">
        <input type="checkbox" name="headerline_include" id="headerline_include" value="1"
           <?php $s->check_select(Html::encode($body['headerline_include'] ?? ''), 1, '==', true) ?>
               disabled="true" >             
    </div>
  </div> 

