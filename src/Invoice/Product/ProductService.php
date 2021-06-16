<?php

declare(strict_types=1);

namespace App\Invoice\Product;

use App\Invoice\Entity\Product;

use Yiisoft\Data\Paginator\KeysetPaginator;

final class ProductService
{
    private const PRODUCTS_FEED_PER_PAGE = 10;
    
    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getFeedPaginator(): KeysetPaginator
    {
        return (new KeysetPaginator($this->repository->getReader()))
            ->withPageSize(self::PRODUCTS_FEED_PER_PAGE);
    }
    
    public function saveProduct(Product $model, ProductForm $form): void
    {
        $model->setProduct_sku($form->getProduct_sku());
        $model->setProduct_name($form->getProduct_name());
        $model->setProduct_description($form->getProduct_description());
        $model->setProduct_price($form->getProduct_price());
        $model->setPurchase_price($form->getPurchase_price());
        $model->setProvider_name($form->getProvider_name());
        $model->setTax_rate_id($form->getTax_rate_id());
        $model->setUnit_id($form->getUnit_id());
        $model->setFamily_id($form->getFamily_id());
        $model->setProduct_tariff($form->getProduct_tariff());
        
        $this->repository->save($model);
    }
    
    public function deleteProduct(Product $model): void
    {
        $this->repository->delete($model);
    }
}
