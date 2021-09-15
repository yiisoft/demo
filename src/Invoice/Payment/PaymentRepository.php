<?php

declare(strict_types=1); 

namespace App\Invoice\Payment;

use App\Invoice\Entity\Payment;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class PaymentRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get payments  without filter
     *
     * @psalm-return DataReaderInterface<int,Payment>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, Payment>
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
    public function save(Payment $payment): void
    {
        $this->entityWriter->write([$payment]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Payment $payment): void
    {
        $this->entityWriter->delete([$payment]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoPaymentquery(string $id): Payment    {
        $query = $this->select()->load('inv')->load('payment_method')->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}