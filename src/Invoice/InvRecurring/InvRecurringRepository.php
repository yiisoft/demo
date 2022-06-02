<?php

declare(strict_types=1); 

namespace App\Invoice\InvRecurring;

use App\Invoice\Entity\InvRecurring;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class InvRecurringRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get invrecurrings  without filter
     *
     * @psalm-return DataReaderInterface<int,InvRecurring>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, InvRecurring>
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
    public function save(InvRecurring $invrecurring): void
    {
        $this->entityWriter->write([$invrecurring]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(InvRecurring $invrecurring): void
    {
        $this->entityWriter->delete([$invrecurring]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoInvRecurringquery(string $id): InvRecurring    {
        $query = $this->select()->where(['id' =>$id]);
        return  $query->fetchOne();        
    }
    
    public function repoCount(string $id) : int {
        $query = $this->select()
                      ->where(['inv_id' => $id]);
        return $query->count();
    }
        
    public function recur_frequencies() 
    { 
        $recur_frequencies = [
            '1D' => 'calendar_day_1',
            '2D' => 'calendar_day_2',
            '3D' => 'calendar_day_3',
            '4D' => 'calendar_day_4',
            '5D' => 'calendar_day_5',
            '6D' => 'calendar_day_6',
            '15D' => 'calendar_day_15',
            '30D' => 'calendar_day_30',
            '7D' => 'calendar_week_1',
            '14D' => 'calendar_week_2',
            '21D' => 'calendar_week_3',
            '28D' => 'calendar_week_4',
            '1M' => 'calendar_month_1',
            '2M' => 'calendar_month_2',
            '3M' => 'calendar_month_3',
            '4M' => 'calendar_month_4',
            '5M' => 'calendar_month_5',
            '6M' => 'calendar_month_6',
            '7M' => 'calendar_month_7',
            '8M' => 'calendar_month_8',
            '9M' => 'calendar_month_9',
            '10M' => 'calendar_month_10',
            '11M' => 'calendar_month_11',
            '1Y' => 'calendar_year_1',
            '2Y' => 'calendar_year_2',
            '3Y' => 'calendar_year_3',
            '4Y' => 'calendar_year_4',
            '5Y' => 'calendar_year_5',
        ];
        return $recur_frequencies;
    }
    
    // Recur invoices become active when the current date passes the recur_next_date ie. recur_next_date is less than current date
    // They remain active as long as the current date does not pass the recur_end_date or the recur_end_date has been stopped
    // ie. a zero mysql string date is inserted.
    // If they are active the button will indicate active on it. Use the base invoice hyperlink to go to the respective invoice
    
    /**
     * Get invrecurrings  that are active
     *
     * @psalm-return DataReaderInterface<int,InvRecurring>
     */
    public function active(): DataReaderInterface
    {
        $query = $this->select()
                      ->where('next_date','<',date(NOW()))
                      ->orWhere('end_date','>',date(NOW()))
                      ->orWhere('end_date','=','0000-00-00');
        return $this->prepareDataReader($query);
    }
}