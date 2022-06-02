<?php

declare(strict_types=1); 

namespace App\Invoice\Payment;

use App\Invoice\Entity\Payment;


final class PaymentService
{

    private PaymentRepository $repository;

    public function __construct(PaymentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function savePayment(Payment $model, PaymentForm $form): void
    {
       $model->setPayment_method_id($form->getPayment_method_id());
       $model->setPayment_date($form->getPayment_date());
       $model->setAmount($form->getAmount());
       $model->setNote($form->getNote());
       $model->setInv_id($form->getInv_id()); 
       $this->repository->save($model);
    }
    
    public function deletePayment(Payment $model): void
    {
        $this->repository->delete($model);
    }
}