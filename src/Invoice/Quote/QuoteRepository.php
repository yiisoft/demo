<?php

declare(strict_types=1); 

namespace App\Invoice\Quote;

use App\Invoice\Entity\Quote;
use App\Invoice\Setting\SettingRepository as SR;
use Cycle\ORM\Select;
use Throwable;
use Spiral\Database\Injection\Fragment;
use Spiral\Database\Injection\Parameter;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class QuoteRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }
    
    /**
     * Get Quotes with filter
     *
     * @psalm-return DataReaderInterface<int, Quote>
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
     * Get quotes  without filter
     *
     * @psalm-return DataReaderInterface<int,Quote>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select()
                ->load(['client','group','user']);
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, Quote>
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
    public function save(Quote $quote): void
    {
        $this->entityWriter->write([$quote]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Quote $quote): void
    {
        $this->entityWriter->delete([$quote]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoQuoteUnLoadedquery(string $id): Quote    {
        $query = $this->select()->where(['id' => $id]);
        return  $query->fetchOne();        
    }
    
    public function repoQuoteLoadedquery(string $id): Quote    {
        $query = $this->select()
                      ->load(['client','group','user']) 
                      ->where(['id' => $id]);
        return  $query->fetchOne();        
    }
    
    // Used in QuoteAmountRepository
    public function repoQuoteMonth(): DataReaderInterface {
        $query = $this>select()->where(new Fragment("MONTH(date_created)"), '=', new Fragment("MONTH(NOW())"))
                               ->andWhere(new Fragment("YEAR(date_created)"), '=', new Fragment("YEAR(NOW())")) ;        
        return $this->prepareDataReader($query);
    }
    
    public function repoQuoteLastMonth(): DataReaderInterface {
        $query = $this>select()->where(new Fragment("MONTH(date_created)"), '=', new Fragment("MONTH(NOW() - INTERVAL 1 MONTH"))
                               ->andWhere(new Fragment("YEAR(date_created)"), '=', new Fragment("YEAR(NOW() - INTERVAL 1 MONTH")) ;        
        return $this->prepareDataReader($query);
    }
    
    public function repoQuoteYear(): DataReaderInterface {
        $query = $this>select()->where(new Fragment("YEAR(date_created)"), '=', new Fragment("YEAR(NOW())"));        
        return $this->prepareDataReader($query);
    }
    
    public function repoQuoteLastYear(): DataReaderInterface {
        $query = $this>select()->where(new Fragment("YEAR(date_created)"), '=', new Fragment("YEAR(NOW() - INTERVAL 1 YEAR)"));        
        return $this->prepareDataReader($query);
    }
    
    /**
     * @return array
     */
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
     * @param $group_id
     * @return mixed
     */
    public function get_quote_number($group_id, gR $gR)
    {   
        return $gR->generate_invoice_number($group_id);
    }
    
    /**
     * @return $query
     */
    public function is_draft()
    {
        $query = $this->select()->where(['status_id' => 1]);
        return $query;
    }
   
    /**
     * @return $query
     */
    public function is_sent()
    {
        $query = $this->select()->where(['status_id' => 2]);
        return $query;
    }

    /**
     * @return $query
     */
    public function is_viewed()
    {
        $query = $this->select()->where(['status_id' => 3]);
        return $query;
    }

    /**
     * @return $query 
     */
    public function is_approved()
    {
        $query = $this->select()->where(['status_id' => 4]);
        return $query;
    }

    /**
     * @return $query
     */
    public function is_rejected()
    {
        $query = $this->select()->where(['status_id' => 5]);
        return $query;
    }

    /**
     * @return $query
     */
    public function is_canceled()
    {
        $query = $this->select()->where(['status_id' => 6]);
        return $query;
    }

    /**
     * Used by guest; includes only sent and viewed
     *
     * @return $query
     */
    public function is_open()
    {
        $query = $this->select()->where(['status_id'=>['in'=>new Parameter([2,3])]]);
        return $query;
    }

    /**
     * @return $query
     */
    public function guest_visible()
    {
        $query = $this->select()->where(['status_id'=>['in'=>new Parameter([2,3,4,5])]]);
        return $query ;
    }

    /**
     * @param $client_id
     * @return $this
     */
    public function by_client($client_id)
    {
        $query = $this->select()->where(['client_id'=>$client_id]);
        return $query;
    }

    /**
     * @param $url_key
     */
    public function approve_or_reject_quote_by_key($url_key){
        $query = $this->select()->where(['status_id'=>['in'=>new Parameter([2,3])]])
                                ->where(['url_key'=>$url_key]);
        return $query;
    }

    /**
     * @param $id
     */
    public function approve_or_reject_quote_by_id($id){
        $query = $this->select()->where(['status_id'=>['in'=>new Parameter([2,3])]])
                                ->where(['id'=>$id]);
        return $query;
    }
    
    public function repoCount($quote_id) : int {
        $count = $this->select()->where(['id'=>$quote_id])->count();
        return $count;
    }
}