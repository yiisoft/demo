<?php

declare(strict_types=1); 

namespace App\Invoice\Inv;

use App\Invoice\Entity\Inv;
use App\Invoice\Setting\SettingRepository as SR;
use App\Invoice\InvCustom\InvCustomRepository as ICR;
use App\Invoice\InvItem\InvItemRepository as IIR;
use App\Invoice\InvAmount\InvAmountRepository as IAR;
use App\Invoice\InvItemAmount\InvItemAmountRepository as IIAR;
use App\Invoice\Product\ProductRepository as PR;
use App\Invoice\TaxRate\TaxRateRepository as TRR;
use App\Invoice\InvTaxRate\InvTaxRateRepository as ITRR;
use Cycle\ORM\Select;
use Throwable;
use Spiral\Database\Injection\Fragment;
use Spiral\Database\Injection\Parameter;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;
use Yiisoft\Security\Random;
use Yiisoft\Validator\ValidatorInterface as Validator;

final class InvRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }
    
    /**
     * Get Invoices with filter
     *
     * @psalm-return DataReaderInterface<int, Inv>
     */
    public function findAllWithStatus($status_id) : DataReaderInterface
    {
        if (($status_id) > 0) {
        $query = $this->select()
                ->load(['client','group','user'])
                ->where(['status_id' => $status_id]);  
         return $this->prepareDataReader($query);
       } else {
         return $this->findAllPreloaded();  
       }       
    }
    
    /**
     * Get Invoices with filter
     *
     * @psalm-return DataReaderInterface<int, Inv>
     */
    public function findAllWithClient($client_id) : DataReaderInterface
    {
        $query = $this->select()
                ->where(['client_id' => $client_id]);  
        return $this->prepareDataReader($query);
    }
    
    /**
     * Get invoices without filter
     *
     * @psalm-return DataReaderInterface<int,Inv>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()
                ->load(['client','group','user']);
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, Inv>
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
    public function save(Inv $inv): void
    {
        $this->entityWriter->write([$inv]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Inv $inv): void
    {
        $this->entityWriter->delete([$inv]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoInvStatusquery($invoice_id, $status_id) : Inv {
        $query = $this->select()->where(['id' => $invoice_id])
                                ->where(['status_id'=>$status_id]);
        return  $query->fetchOne();        
    }
    
    public function repoInvUnLoadedquery(string $id): Inv    {
        $query = $this->select()->where(['id' => $id]);
        return  $query->fetchOne();        
    }
    
    public function repoInvLoadedquery(string $id): Inv    {
        $query = $this->select()
                      ->load(['client','group','user']) 
                      ->where(['id' => $id]);
        return  $query->fetchOne();        
    }
    
    // Used in InvAmountRepository
    public function repoInvMonth(): DataReaderInterface {
        $query = $this>select()->where(new Fragment("MONTH(date_created)"), '=', new Fragment("MONTH(NOW())"))
                               ->andWhere(new Fragment("YEAR(date_created)"), '=', new Fragment("YEAR(NOW())")) ;        
        return $this->prepareDataReader($query);
    }
    
    public function repoInvLastMonth(): DataReaderInterface {
        $query = $this>select()->where(new Fragment("MONTH(date_created)"), '=', new Fragment("MONTH(NOW() - INTERVAL 1 MONTH"))
                               ->andWhere(new Fragment("YEAR(date_created)"), '=', new Fragment("YEAR(NOW() - INTERVAL 1 MONTH")) ;        
        return $this->prepareDataReader($query);
    }
    
    public function repoInvYear(): DataReaderInterface {
        $query = $this>select()->where(new Fragment("YEAR(date_created)"), '=', new Fragment("YEAR(NOW())"));        
        return $this->prepareDataReader($query);
    }
    
    public function repoInvLastYear(): DataReaderInterface {
        $query = $this>select()->where(new Fragment("YEAR(date_created)"), '=', new Fragment("YEAR(NOW() - INTERVAL 1 YEAR)"));        
        return $this->prepareDataReader($query);
    }
    
    public function open() : DataReaderInterface {
        // 1 draft, 2 sent, 3 viewed, 4 paid
        $query = $this->select()->where(['status_id'=>['in'=> new Parameter(['2','3'])]]);
        return $this->prepareDataReader($query);    
    }
    
    public function getStatuses(SR $s)
    {
        return array(
            '1' => array(
                'label' => $s->trans('draft'),
                'class' => 'draft',
                'href' => 1
            ),
            '2' => array(
                'label' => $s->trans('sent'),
                'class' => 'sent',
                'href' => 2
            ),
            '3' => array(
                'label' => $s->trans('viewed'),
                'class' => 'viewed',
                'href' => 3
            ),
            '4' => array(
                'label' => $s->trans('approved'),
                'class' => 'approved',
                'href' => 4
            ),
            '5' => array(
                'label' => $s->trans('rejected'),
                'class' => 'rejected',
                'href' => 5
            ),
            '6' => array(
                'label' => $s->trans('canceled'),
                'class' => 'canceled',
                'href' => 6
            )
        );       
    }
    
    /**
     * @param string $invoice_date_created
     * @return string
     */
    public function get_date_due($invoice_date_created)
    {
        $invoice_date_due = new DateTime($invoice_date_created);
        $invoice_date_due->add(new DateInterval('P' . get_setting('invoices_due_after') . 'D'));
        return $invoice_date_due->format('Y-m-d');
    }
    
    /**
     * @return string
     */
    public function get_url_key()
    {
        $random = new Random();
        return $random::string(32);
    }
    
    /**
     * @param $group_id
     * @return mixed
     */
    public function get_inv_number($group_id, gR $gR)
    {   
        return $gR->generate_invoice_number($group_id);
    }
    
    public function with_total($client_id, IAR $iaR)
    {
        $invoices = $this->findAllWithClient($client_id);
        $sum = 0.00;
        foreach ($invoices as $invoice) {
            $invoice_amount = ($iaR->repoInvAmountCount((string)$invoice->getId())> 0 ? $iaR->repoInvquery($invoice->getId()) : null);            
            $sum += (null!==$invoice_amount ? $invoice_amount->getTotal() : 0.00);
        }
        return $sum;
    }

    public function with_total_paid($client_id, IAR $iaR)
    {
        $invoices = $this->findAllWithClient($client_id);
        $sum = 0.00;
        foreach ($invoices as $invoice) {
            $invoice_amount = ($iaR->repoInvAmountCount((string)$invoice->getId())> 0 ? $iaR->repoInvquery($invoice->getId()) : null); 
            $sum += (null!==$invoice_amount ? $invoice_amount->getPaid() : 0.00);
        }
        return $sum;
    }

    public function with_total_balance($client_id, IAR $iaR)
    {
        $invoices = $this->findAllWithClient($client_id);
        $sum = 0.00;
        foreach ($invoices as $invoice) {
            $invoice_amount = ($iaR->repoInvAmountCount((string)$invoice->getId())> 0 ? $iaR->repoInvquery($invoice->getId()) : null); 
            $sum += (null!==$invoice_amount ? $invoice_amount->getBalance() : 0.00);
        }
        return $sum;
    } 
    
    /**
     * Copies invoice items, tax rates, etc from source to target
     * @param int $inv_id
     * @param int $copy_id
     * @param bool $copy_recurring_items_only
     */
    public function copy_invoice($inv_id, $copy_id, $copy_recurring_items_only, 
                                 IAR $iaR, 
                                 ICR $icR,
                                 IIAR $iiaR,
                                 IIAS $iiaS,
                                 PR $pR,
                                 IIR $iiR, 
                                 ITRR $itrR, 
                                 TRR $trR, 
                                 Validator $validator)
    {
        // Copy the items
        $items = $iiR->repoInvItemIdquery((string)$inv_id);
        foreach ($items as $inv_item) {
            $copy_item = [
                'inv_id'=>$copy_id,
                'tax_rate_id'=>$inv_item->getTax_rate_id(),
                'product_id'=>$inv_item->getProduct_id(),
                'task_id'=>$inv_item->getTask_id(),
                'name'=>$inv_item->getName(),
                'description'=>$inv_item->getDescription(),
                'quantity'=>$inv_item->getQuantity(),
                'price'=>$inv_item->getPrice(),
                'discount_amount'=>$inv_item->getDiscount_amount(),
                'order'=>$inv_item->getOrder(),
                'is_recurring'=>$inv_item->getIs_recurring(),
                'product_unit'=>$inv_item->getProduct_unit(),
                'product_unit_id'=>$inv_item->getProduct_unit_id(),
                // Recurring date
                'date'=>''
            ];
            if (!$copy_recurring_items_only || $inv_item->getIs_recurring()) {
                // Create an equivalent invoice item for the invoice item
                $copyitem = new InvItem();
                $invitemform = new InvItemForm();
                ($invitemform->load($copy_item) && $validator->validate($invitemform)->isValid()) 
                ? $this->invItemService->saveInvItem($copyitem, $invitemform, $copy_id, $pR, $trR , $iiaS, $iiaR)
                : '';                
            }
        }

        // Get all tax rates that have been setup for the invoice
        $inv_tax_rates = $itrR->repoInvquery($inv_id);        
        foreach ($inv_tax_rates as $inv_tax_rate){            
            $copy_tax_rate = [
                'inv_id'=>$copy_id,
                'tax_rate_id'=>$inv_tax_rate->getTax_rate_id(),
                'include_item_tax'=>$inv_tax_rate->getInclude_item_tax(),
                'amount'=>$inv_tax_rate->getInv_tax_rate_amount(),
            ];
            $invtaxrate = new InvTaxRate();
            $invtaxrateform = new InvTaxRateForm();
            if ($invtaxrateform->load($copy_tax_rate) && $validator->validate($invtaxrateform)->isValid()) {    
                $this->invTaxRateService->saveInvTaxRate($invtaxrate,$invtaxrateform);
            }
        }
        
        $inv_customs = $icR->repoFields($inv_id);
        foreach ($inv_customs as $inv_custom) {
            $copy_custom = [
                'inv_id'=>$copy_id,
                'custom_field_id'=>$inv_custom->getCustom_field_id(),
                'value'=>$inv_custom->getValue(),
            ];
            $entity = new InvCustom();
            $invcustomform = new InvCustomForm();
            if ($invcustomform->load($copy_custom) && $validator->validate($invcustomform)->isValid()) {    
                $this->invCustomService->saveInvCustom($entity,$invcustomform);            
            }
        } 
        
        $inv_amount = $iaR->repoInvquery($inv_id);
        $copy_amount = [
            'inv_id'=>$copy_id,
            'sign'=>1,
            'item_sub_total'=>$inv_amount->getItem_subtotal(),
            'item_tax_total'=>$inv_amount->getItem_tax_total(),
            'tax_total'=>$inv_amount->getTax_total(),
            'inv_total'=>$inv_amount->getTotal(),
            'inv_paid'=>floatval(0.00),
            'inv_balance'=>floatval(0.00),
        ];
        $entity = new InvAmount();
        $invamountform = new InvAmountForm();
        if ($invamountform->load($copy_amount) && $validator->validate($invamountform)->isValid()) {    
                $this->invAmountService->saveInvAmount($entity,$invamountform);            
        }        
    }
    
    public function repoCount($id) : int {
        $count = $this->select()
                ->where(['id' => $id]) 
                ->count();
        return $count;
    }    
}