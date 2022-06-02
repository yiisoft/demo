<?php

declare(strict_types=1); 

namespace App\Invoice\QuoteAmount;

use App\Invoice\Entity\QuoteAmount;
use App\Invoice\Quote\QuoteRepository as QR;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class QuoteAmountRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get quoteamounts  without filter
     *
     * @psalm-return DataReaderInterface<int,QuoteAmount>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, QuoteAmount>
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
    public function save(QuoteAmount $quoteamount): void
    {
        $this->entityWriter->write([$quoteamount]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(QuoteAmount $quoteamount): void
    {
        $this->entityWriter->delete([$quoteamount]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoQuoteAmountCount(string $quote_id) : int {
        $query = $this->select()
                      ->where(['quote_id' => $quote_id]);
        return $query->count();
    }   
    
    public function repoQuoteAmountquery(string $id): QuoteAmount {
        $query = $this->select()->load('quote')->where(['id' => $id]);
        return  $query->fetchOne();        
    }    
    
    public function repoQuotequery(string $quote_id): QuoteAmount {
        $query = $this->select()->load('quote')->where(['quote_id' => $quote_id]);
        return  $query->fetchOne();        
    }
   
    /**
     * @param string $period
     * @return array
     */
    public function get_status_totals(QR $qR, SR $sR, $period = '')
    {
        switch ($period) {
            default:
            case 'this-month':
                $results = $this->select('quote_status_id',"SUM(quote_total) AS sum_total","COUNT(*) AS num_total")
                                ->join('quote')
                                ->on('quote.id','quote_amount.quote_id')
                                ->andOn(new Fragment("MONTH(quote.date_created)"),'=', new Fragment("MONTH(NOW())"))
                                ->andOn(new Fragment("YEAR(quote.date_created)"),'=', new Fragment("YEAR(NOW())"))
                                ->groupBy('quote.status_id');
                break;
            case 'last-month':
                $results = $this->select('quote_status_id',"SUM(quote_total) AS sum_total","COUNT(*) AS num_total")
                                ->join('quote')
                                ->on('quote.id','quote_amount.quote_id')
                                ->andOn(new Fragment("MONTH(quote.date_created)"), '=', new Fragment("MONTH(NOW() - INTERVAL 1 MONTH"))
                                ->andOn(new Fragment("YEAR(quote.date_created)"), '=', new Fragment("YEAR(NOW() - INTERVAL 1 MONTH"))
                                ->groupBy('quote.status_id');
                break;
            case 'this-quarter':
                $results = $this->select('quote_status_id',"SUM(quote_total) AS sum_total","COUNT(*) AS num_total")
                                ->join('quote')
                                ->on('quote.id','quote_amount.quote_id')
                                ->andOn(new Fragment("QUARTER(quote.date_created)"), '=', new Fragment("QUARTER(NOW())"))
                                ->andOn(new Fragment("YEAR(quote.date_created)"), '=', new Fragment("YEAR(NOW())"))
                                ->groupBy('quote.status_id');
                break;
            case 'last-quarter':
                $results = $this->select('quote_status_id',"SUM(quote_total) AS sum_total","COUNT(*) AS num_total")
                                ->join('quote')
                                ->on('quote.id','quote_amount.quote_id')
                                ->andOn(new Fragment("QUARTER(quote.date_created)"), '=', new Fragment("QUARTER(NOW()- INTERVAL 1 QUARTER)"))
                                ->andOn(new Fragment("YEAR(quote.date_created)"), '=', new Fragment("YEAR(NOW())"))
                                ->groupBy('quote.status_id');
                break;
            case 'this-year':
                $results = $this->select('quote_status_id',"SUM(quote_total) AS sum_total","COUNT(*) AS num_total")
                                ->join('quote')
                                ->on('quote.id','quote_amount.quote_id')
                                ->andOn(new Fragment("YEAR(quote.date_created)"), '=', new Fragment("YEAR(NOW())"))
                                ->groupBy('quote.status_id');
                break;
            case 'last-year':
                $results = $this->select('quote_status_id',"SUM(quote_total) AS sum_total","COUNT(*) AS num_total")
                                ->join('quote')
                                ->on('quote.id','quote_amount.quote_id')
                                ->andOn(new Fragment("YEAR(quote.date_created)"), '=', new Fragment("YEAR(NOW() - INTERVAL 1 YEAR)"))
                                ->groupBy('quote.status_id');
                break;
        }

        $return = [];

        foreach ($qR->getStatuses($sR) as $key => $status) {
            $return[$key] = [
                'quote_status_id' => $key,
                'class' => $status['class'],
                'label' => $status['label'],
                'href' => $status['href'],
                'sum_total' => 0,
                'num_total' => 0
            ];
        }

        foreach ($results as $result) {
            $return[$result['quote_status_id']] = array_merge($return[$result['quote_status_id']], $result);
        }

        return $return;
    }
}