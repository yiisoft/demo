<?php

declare(strict_types=1); 

namespace App\Invoice\PaymentMethod;

use App\Invoice\Entity\PaymentMethod;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class PaymentMethodRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get paymentmethods  without filter
     *
     * @psalm-return DataReaderInterface<int,PaymentMethod>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, PaymentMethod>
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
    public function save(PaymentMethod $paymentmethod): void
    {
        $this->entityWriter->write([$paymentmethod]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(PaymentMethod $paymentmethod): void
    {
        $this->entityWriter->delete([$paymentmethod]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoPaymentMethodquery(string $id): PaymentMethod    {
        $query = $this->select()->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}