<?php

declare(strict_types=1);

namespace App\Invoice\Product;

use App\Invoice\Entity\Product;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Spiral\Database\Injection\Parameter;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class ProductRepository extends Select\Repository
{
    private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get products without filter
     *
     * @psalm-return DataReaderInterface<int, Product>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()
            ->load('family')
            ->load('tax_rate')
            ->load('unit');
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, Product>
     */
    public function getReader(): DataReaderInterface
    {
        return (new EntityReader($this->select()))
            ->withSort($this->getSort());
    }
    
    private function getSort(): Sort
    {
        return Sort::only(['id'])->withOrder(['id' => 'asc']);
    }

    /**
     * @throws Throwable
     */
    public function save(Product $product): void
    {
        $this->entityWriter->write([$product]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Product $product): void
    {
        $this->entityWriter->delete([$product]);
    }
    

    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id', 'product_description'])
                ->withOrder(['product_description' => 'desc'])
        );
    }
    
    public function repoProductquery($product_id): Product
    {
        $query = $this
            ->select()
            ->load('family')
            ->load('tax_rate')
            ->load('unit')
            ->where(['id' => $product_id]);
        return  $query->fetchOne();        
    }
    
    /**
     * Get products with filter
     *
     * @psalm-return DataReaderInterface<int, Product>
     */
    
    public function repoProductwithfamilyquery(string $product_name, string $family_id): DataReaderInterface
    {
        $query = $this
            ->select()
            ->load('family')
            ->load('tax_rate')
            ->load('unit');

        //lookup without filters eg. product/lookup
        if (empty($product_name)&&(empty($family_id)||$family_id===(string)0)) {}
                
        //eg. product/lookup?fp=Cleaning%20Services
        if ((!empty($product_name))&&(empty($family_id))) {      
            $query = $query->where(['product_name' => ltrim(rtrim($product_name))]);
        }
        
        //eg. product/lookup?Cleaning%20Services&ff=4
        if (!empty($product_name)&&($family_id>(string)0)) {      
            $query = $query->where(['family_id'=>$family_id])->andWhere(['product_name' => ltrim(rtrim($product_name))]);
        }
        
        //eg. product/lookup?ff=4
        if (empty($product_name)&&($family_id>(string)0)) {                  
            $query = $query->where(['family_id'=>$family_id]);
        }
        
        return $this->prepareDataReader($query);
    } 
    
     /**
     * Get selection of products from all products
     *
     * @psalm-return DataReaderInterface<int, Product>
     */
    
    public function findinProducts($product_ids) : DataReaderInterface {
        $query = $this
        ->select()
        ->where(['id'=>['in'=> new Parameter($product_ids)]]);
        return $this->prepareDataReader($query);    
    } 
    
    public function repoCount($product_id): int {
        $count = $this->select()
                      ->where(['id' => $product_id])
                      ->count();
        return $count;   
    }
}
