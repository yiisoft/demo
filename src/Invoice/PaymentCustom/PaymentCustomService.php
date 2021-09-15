<?php

declare(strict_types=1); 

namespace App\Invoice\PaymentCustom;

use App\Invoice\Entity\PaymentCustom;


final class PaymentCustomService
{

    private PaymentCustomRepository $repository;

    public function __construct(PaymentCustomRepository $repository)
    {
        $this->repository = $repository;
    }

    public function savePaymentCustom(PaymentCustom $model, PaymentCustomForm $form): void
    {
        
       $model->setPayment_id($form->getPayment_id());
       $model->setFieldid($form->getFieldid());
       $model->setFieldvalue($form->getFieldvalue());
 
        $this->repository->save($model);
    }
    
    public function deletePaymentCustom(PaymentCustom $model): void
    {
        $this->repository->delete($model);
    }
}