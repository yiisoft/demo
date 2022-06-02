<?php
declare(strict_types=1); 

namespace App\Invoice\Quote;
// Entities
use App\Invoice\Entity\Quote;
use App\User\User;
// Repositories
use App\Invoice\QuoteAmount\QuoteAmountRepository as QAR;
use App\Invoice\QuoteCustom\QuoteCustomRepository as QCR;
use App\Invoice\QuoteItem\QuoteItemRepository as QIR;
use App\Invoice\QuoteTaxRate\QuoteTaxRateRepository as QTRR;
use App\Invoice\Quote\QuoteRepository as QR;
use App\Invoice\Setting\SettingRepository as SR;
use App\Invoice\Group\GroupRepository as GR;
// Services
use App\Invoice\QuoteAmount\QuoteAmountService as QAS;
use App\Invoice\QuoteCustom\QuoteCustomService as QCS;
use App\Invoice\QuoteItem\QuoteItemService as QIS;
use App\Invoice\QuoteTaxRate\QuoteTaxRateService as QTRS;
use DateTimeImmutable;
// Ancillary
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Session\SessionInterface;

final class QuoteService
{
    private QuoteRepository $repository;
    private SessionInterface $session;

    public function __construct(QR $repository, SessionInterface $session)
    {
        $this->repository = $repository;
        $this->session = $session;        
    }

    public function saveQuote(User $user, Quote $model, QuoteForm $form, SR $s): void
    { 
        $model->setInv_id($form->getInv_id());
        $model->setClient_id($form->getClient_id());
        $model->setGroup_id($form->getGroup_id());
        $model->setStatus_id($form->getStatus_id());
        $model->setDiscount_percent($form->getDiscount_percent());
        $model->setDiscount_amount($form->getDiscount_amount());
        $model->setUrl_key($form->getUrl_key());
        $model->setPassword($form->getPassword());
        $model->setNotes($form->getNotes());       
        if ($model->isNewRecord()) {
             $model->setInv_id(0);             
             $model->setNumber($form->getNumber());
             $model->setStatus_id(1);
             $model->setUser($user);
             $model->setUrl_key(random_bytes(32));            
             $model->setDate_created(new DateTimeImmutable('now'));
             $model->setDate_expires($s);
             $model->setDiscount_amount(0.00);
        }
        $this->repository->save($model);
    }
    
    public function deleteQuote(Quote $model, QCR $qcR, QCS $qcS, QIR $qiR, QIS $qiS, QTRR $qtrR, QTRS $qtrS, QAR $qaR, QAS $qaS): void
    {
        $quote_id = $model->getId();
        // Quotes with no items: If there are no quote items there will be no quote amount record
        // so check if there is a quote amount otherwise null error will occur.
        $count = $qaR->repoQuoteAmountCount($quote_id);        
        if ($count > 0) {
            $quote_amount = $qaR->repoQuotequery((string)$quote_id);
            $qaS->deleteQuoteAmount($quote_amount);            
        }
        foreach ($qiR->repoQuoteItemIdquery((string)$quote_id) as $item) {
                 $qiS->deleteQuoteItem($item);
        }        
        foreach ($qtrR->repoQuotequery((string)$quote_id) as $quote_tax_rate) {
                 $qtrS->deleteQuoteTaxRate($quote_tax_rate);
        }
        foreach ($qcR->repoFields((string)$quote_id) as $quote_custom) {
                 $qcS->deleteQuoteCustom($quote_custom);
        }
        $this->repository->delete($model);
    }
    
    private function flash($level, $message){
        $flash = new Flash($this->session);
        $flash->set($level, $message); 
        return $flash;
    }
}