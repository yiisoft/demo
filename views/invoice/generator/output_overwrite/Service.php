<?php

declare(strict_types=1); 

namespace App\Invoice\Product;

use App\Invoice\Entity\Product;


final class ProductService
{

    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveProduct(Product $model, ProductForm $form): void
    {
        
       $model->setId($form->getId());
       $model->setProduct_sku($form->getProduct_sku());
       $model->setProduct_name($form->getProduct_name());
       $model->setProduct_description($form->getProduct_description());
       $model->setProduct_price($form->getProduct_price());
       $model->setPurchase_price($form->getPurchase_price());
       $model->setProvider_name($form->getProvider_name());
       $model->setFamily_id($form->getFamily_id());
       $model->setTax_rate_id($form->getTax_rate_id());
       $model->setUnit_id($form->getUnit_id());
       $model->setProduct_tariff($form->getProduct_tariff());
  
    }
    
    public function deleteProduct(Product $model): void
    {
        $this->repository->delete($model);
    }
}