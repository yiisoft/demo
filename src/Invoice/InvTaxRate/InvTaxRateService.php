<?php
declare(strict_types=1); 

namespace App\Invoice\InvTaxRate;

use App\Invoice\Entity\InvTaxRate;


final class InvTaxRateService
{
    private InvTaxRateRepository $repository;

    public function __construct(InvTaxRateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveInvTaxRate(InvTaxRate $model, InvTaxRateForm $form): void
    {        
        $model->setInv_id($form->getInv_id());
        $model->setTax_rate_id($form->getTax_rate_id());
        $model->setInclude_item_tax($form->getInclude_item_tax());
        $model->setInv_tax_rate_amount($form->getInv_tax_rate_amount()); 
        $this->repository->save($model);
    }
    
    public function initializeCreditInvTaxRate($basis_inv_id, $new_inv_id) : void
    {
        $basis_invoice_tax_rates = $this->repository->repoInvquery($basis_inv_id);
        foreach ($basis_invoice_tax_rates as $basis_invoice_tax_rate) {
            $new_invoice_tax_rate = new InvTaxRate();
            $new_invoice_tax_rate->setInv_id((int)$new_inv_id);
            $new_invoice_tax_rate->setTax_rate_id((int)$basis_invoice_tax_rate->getTax_rate_id());
            $new_invoice_tax_rate->setInclude_item_tax($basis_invoice_tax_rate->getInclude_item_tax());
            $new_invoice_tax_rate->setInv_tax_rate_amount($basis_invoice_tax_rate->getInv_tax_rate_amount()*-1); 
            $this->repository->save($new_invoice_tax_rate);
        }
    }
    
    public function deleteInvTaxRate(InvTaxRate $model): void
    {
        $this->repository->delete($model);
    }
}