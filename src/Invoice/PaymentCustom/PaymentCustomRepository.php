<?php

declare(strict_types=1); 

namespace App\Invoice\PaymentCustom;

use App\Invoice\Entity\PaymentCustom;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class PaymentCustomRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get paymentcustoms  without filter
     *
     * @psalm-return DataReaderInterface<int,PaymentCustom>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()->load('payment')->load('custom_field');
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, PaymentCustom>
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
    public function save(PaymentCustom $paymentcustom): void
    {
        $this->entityWriter->write([$paymentcustom]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(PaymentCustom $paymentcustom): void
    {
        $this->entityWriter->delete([$paymentcustom]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoPaymentCustomquery(string $id): PaymentCustom    {
        $query = $this->select()->load('payment')
            ->load('custom_field')
            ->where(['id' =>$id]);
        return  $query->fetchOne();        
    }
    
    public function repoFormValuequery(string $payment_id, string $custom_field_id): PaymentCustom {
        $query = $this->select()->where(['payment_id' =>$payment_id])
                                ->andWhere(['custom_field_id' =>$custom_field_id]);
        return  $query->fetchOne();        
    }
    
    public function repoPaymentCustomCount(string $payment_id, string $custom_field_id) : int {
        $query = $this->select()->where(['payment_id' =>$payment_id])
                                ->andWhere(['custom_field_id' =>$custom_field_id]);
        return $query->count();
    } 
    
    public function repoPaymentCount(string $payment_id) : int {
        $query = $this->select()->where(['payment_id' =>$payment_id]);
        return $query->count();
    }   
    
    /**
     * Get all fields that have been setup for a particular payment
     *
     * @psalm-return DataReaderInterface<int,PaymentCustom>
     */
    public function repoFields(string $payment_id): DataReaderInterface
    {
        $query = $this->select()->where(['payment_id'=>$payment_id]);                
        return $this->prepareDataReader($query);
    }
}