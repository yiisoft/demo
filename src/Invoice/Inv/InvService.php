<?php
declare(strict_types=1); 

namespace App\Invoice\Inv;
// Entities
use App\Invoice\Entity\Inv;
use App\User\User;
// Repositories
use App\Invoice\InvAmount\InvAmountRepository as IAR;
use App\Invoice\InvCustom\InvCustomRepository as ICR;
use App\Invoice\InvItem\InvItemRepository as IIR;
use App\Invoice\InvTaxRate\InvTaxRateRepository as ITRR;
use App\Invoice\Inv\InvRepository as IR;
use App\Invoice\Setting\SettingRepository as SR;
// Services
use App\Invoice\InvAmount\InvAmountService as IAS;
use App\Invoice\InvCustom\InvCustomService as ICS;
use App\Invoice\InvItem\InvItemService as IIS;
use App\Invoice\InvTaxRate\InvTaxRateService as ITRS;
use DateTimeImmutable;
// Ancillary
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Security\Random;

final class InvService
{
    private InvRepository $repository;
    private SessionInterface $session;

    public function __construct(IR $repository, SessionInterface $session)
    {
        $this->repository = $repository;
        $this->session = $session;    
    }

    public function saveInv(User $user, Inv $model, InvForm $form, SR $s): void
    {        
       $model->setClient_id($form->getClient_id());
       $model->setGroup_id($form->getGroup_id());
       $model->setStatus_id($form->getStatus_id());
       $model->setDiscount_percent($form->getDiscount_percent());
       $model->setDiscount_amount($form->getDiscount_amount());
       $model->setUrl_key($form->getUrl_key());
       $model->setPassword($form->getPassword());
       $model->setPayment_method($form->getPayment_method());
       $model->setTerms($form->getTerms()); 
       $model->setCreditinvoice_parent_id($form->getCreditinvoice_parent_id());
       if ($model->isNewRecord()) {
            $model->setStatus_id(1);            
            $model->setNumber($form->getNumber());
            $random = new Random();            
            $model->setUser($user);
            $model->setUrl_key($random::string(32));            
            $model->setDate_created(new DateTimeImmutable('now'));
            $model->setTime_created(date('H:i:s'));
            $model->setPayment_method(0);
            $model->setDate_due($s);
            $model->setDiscount_amount(0.00);
       }
       $this->repository->save($model);
    }
    
    public function deleteInv(Inv $model, ICR $icR, ICS $icS, IIR $iiR, IIS $iiS, ITRR $itrR, ITRS $itrS, IAR $iaR, IAS $iaS): void
    {
        $inv_id = $model->getId();
        // Invs with no items: If there are no invoice items there will be no invoice amount record
        // so check if there is a invoice amount otherwise null error will occur.
        $count = $iaR->repoInvAmountCount($inv_id);        
        if ($count > 0) {
            $inv_amount = $iaR->repoInvquery((string)$inv_id);
            $iaS->deleteInvAmount($inv_amount);            
        }
        foreach ($iiR->repoInvItemIdquery((string)$inv_id) as $item) {
                 $iiS->deleteInvItem($item);
        }        
        foreach ($itrR->repoInvquery((string)$inv_id) as $inv_tax_rate) {
                 $itrS->deleteInvTaxRate($inv_tax_rate);
        }
        foreach ($icR->repoFields((string)$inv_id) as $inv_custom) {
                 $icS->deleteInvCustom($inv_custom);
        }
        $this->repository->delete($model);
    }
    
    private function flash($level, $message){
        $flash = new Flash($this->session);
        $flash->set($level, $message); 
        return $flash;
    }
}

