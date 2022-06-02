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
        <label for="product_sku" class="form-label" style="background:lightblue"><?= $s->trans('product_sku'); ?></label>
        <?= Html::encode($body['product_sku'] ?? '') ?>
    </div>
    <div class="mb-3 form-group">
        <label for="product_name" class="form-label" style="background:lightblue"><?= $s->trans('product_name'); ?></label>
        <?= Html::encode($body['product_name'] ?? '') ?>
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="product_description" class="form-label" style="background:lightblue"><?php echo $s->trans('product_description'); ?></label>
        <?= Html::encode($body['product_description'] ?? '') ?>         
    </div>  
  </div>
  <div class="row">
    <div class="mb-3 form-group">
        <label for="product_price" class="form-label" style="background:lightblue"><?= $s->trans('product_price'); ?></label>
        <?= Html::encode($body['product_price'] ?? '') ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="purchase_price" class="form-label" style="background:lightblue"><?= $s->trans('purchase_price'); ?></label>
        <?= Html::encode($body['purchase_price'] ?? '') ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="provider_name" class="form-label" style="background:lightblue"><?= $s->trans('provider_name'); ?></label>
        <?= Html::encode($body['provider_name'] ?? '') ?>
    </div>    
    <div class="mb-3 form-group">
        <label for="tax_rate_id" class="form-label" style="background:lightblue"><?= $s->trans('tax_rate'); ?></label>
        <?= $product->getTaxrate()->getTax_rate_name();?>
    </div>    
    <div class="mb-3 form-group">
        <label for="unit_id" class="form-label" style="background:lightblue"><?= $s->trans('unit'); ?></label>
        <?= $product->getUnit()->getUnit_name();?>
    </div>
    <div class="mb-3 form-group">
        <label for="family_id" class="form-label" style="background:lightblue"><?= $s->trans('family'); ?></label>
        <?= $product->getFamily()->getFamily_name();?>
    </div>        
    <div class="mb-3 form-group">
        <label for="product_tariff" class="form-label" style="background:lightblue"><?= $s->trans('product_tariff'); ?></label>
        <?= Html::encode($body['product_tariff'] ?? '') ?>            
    </div>  
  </div>

      

