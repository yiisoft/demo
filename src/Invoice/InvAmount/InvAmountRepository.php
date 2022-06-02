<?php
declare(strict_types=1); 

namespace App\Invoice\InvAmount;

use App\Invoice\Entity\InvAmount;
use App\Invoice\Inv\InvRepository as IR;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class InvAmountRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get invamounts  without filter
     *
     * @psalm-return DataReaderInterface<int, InvAmount>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, InvAmount>
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
    public function save(InvAmount $invamount): void
    {
        $this->entityWriter->write([$invamount]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(InvAmount $invamount): void
    {
        $this->entityWriter->delete([$invamount]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoInvAmountCount(string $inv_id) : int {
        $query = $this->select()
                      ->where(['inv_id' => $inv_id]);
        return $query->count();
    }   
    
    public function repoInvAmountquery(string $id): InvAmount {
        $query = $this->select()->load('inv')->where(['id' => $id]);
        return  $query->fetchOne();        
    }    
    
    public function repoInvquery(string $inv_id): InvAmount {
        $query = $this->select()->where(['inv_id' => $inv_id]);
        return  $query->fetchOne();        
    }
   
    /**
     * @param string $period
     * @return array
     */
    public function get_status_totals(IR $iR, SR $sR, $period = '')
    {
        switch ($period) {
            default:
            case 'this-month':
                $results = $this->select('inv_status_id',"SUM(inv_total) AS sum_total","COUNT(*) AS num_total")
                                ->join('inv')
                                ->on('inv.id','inv_amount.inv_id')
                                ->andOn(new Fragment("MONTH(inv.date_created)"),'=', new Fragment("MONTH(NOW())"))
                                ->andOn(new Fragment("YEAR(inv.date_created)"),'=', new Fragment("YEAR(NOW())"))
                                ->groupBy('inv.status_id');
                break;
            case 'last-month':
                $results = $this->select('inv_status_id',"SUM(inv_total) AS sum_total","COUNT(*) AS num_total")
                                ->join('inv')
                                ->on('inv.id','inv_amount.inv_id')
                                ->andOn(new Fragment("MONTH(inv.date_created)"), '=', new Fragment("MONTH(NOW() - INTERVAL 1 MONTH"))
                                ->andOn(new Fragment("YEAR(inv.date_created)"), '=', new Fragment("YEAR(NOW() - INTERVAL 1 MONTH"))
                                ->groupBy('inv.status_id');
                break;
            case 'this-quarter':
                $results = $this->select('inv_status_id',"SUM(inv_total) AS sum_total","COUNT(*) AS num_total")
                                ->join('inv')
                                ->on('inv.id','inv_amount.inv_id')
                                ->andOn(new Fragment("QUARTER(inv.date_created)"), '=', new Fragment("QUARTER(NOW())"))
                                ->andOn(new Fragment("YEAR(inv.date_created)"), '=', new Fragment("YEAR(NOW())"))
                                ->groupBy('inv.status_id');
                break;
            case 'last-quarter':
                $results = $this->select('inv_status_id',"SUM(inv_total) AS sum_total","COUNT(*) AS num_total")
                                ->join('inv')
                                ->on('inv.id','inv_amount.inv_id')
                                ->andOn(new Fragment("QUARTER(inv.date_created)"), '=', new Fragment("QUARTER(NOW()- INTERVAL 1 QUARTER)"))
                                ->andOn(new Fragment("YEAR(inv.date_created)"), '=', new Fragment("YEAR(NOW())"))
                                ->groupBy('inv.status_id');
                break;
            case 'this-year':
                $results = $this->select('inv_status_id',"SUM(inv_total) AS sum_total","COUNT(*) AS num_total")
                                ->join('inv')
                                ->on('inv.id','inv_amount.inv_id')
                                ->andOn(new Fragment("YEAR(inv.date_created)"), '=', new Fragment("YEAR(NOW())"))
                                ->groupBy('inv.status_id');
                break;
            case 'last-year':
                $results = $this->select('inv_status_id',"SUM(inv_total) AS sum_total","COUNT(*) AS num_total")
                                ->join('inv')
                                ->on('inv.id','inv_amount.inv_id')
                                ->andOn(new Fragment("YEAR(inv.date_created)"), '=', new Fragment("YEAR(NOW() - INTERVAL 1 YEAR)"))
                                ->groupBy('inv.status_id');
                break;
        }

        $return = [];

        foreach ($iR->getStatuses($sR) as $key => $status) {
            $return[$key] = [
                'inv_status_id' => $key,
                'class' => $status['class'],
                'label' => $status['label'],
                'href' => $status['href'],
                'sum_total' => 0,
                'num_total' => 0
            ];
        }
        foreach ($results as $result) {
            $return[$result['inv_status_id']] = array_merge($return[$result['inv_status_id']], $result);
        }
        return $return;
    }
}