<?php

declare(strict_types=1); 

namespace App\Invoice\Group;

use App\Invoice\Entity\Group;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class GroupRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get groups  without filter
     *
     * @psalm-return DataReaderInterface<int,Group>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, Group>
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
    public function save(Group $group): void
    {
        $this->entityWriter->write([$group]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Group $group): void
    {
        $this->entityWriter->delete([$group]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    /**
     * @param $invoice_group_id
     * @param bool $set_next
     * @return mixed
     */
    public function generate_invoice_number($invoice_group_id, $set_next = true)
    {
        $invoice_group = $this->repoGroupquery((string)$invoice_group_id);

        $invoice_identifier = $this->parse_identifier_format(
            $invoice_group->identifier_format,
            $invoice_group->next_id,
            $invoice_group->left_pad
        );

        if ($set_next) {
            $this->set_next_invoice_number($invoice_group_id);
        }

        return $invoice_identifier;
    }
    
    /**
     * @param $identifier_format
     * @param $next_id
     * @param $left_pad
     * @return mixed
     */
    private function parse_identifier_format($identifier_format, $next_id, $left_pad)
    {
        if (preg_match_all('/{{{([^{|}]*)}}}/', $identifier_format, $template_vars)) {
            foreach ($template_vars[1] as $var) {
                switch ($var) {
                    case 'year':
                        $replace = date('Y');
                        break;
                    case 'yy':
                        $replace = date('y');
                        break;
                    case 'month':
                        $replace = date('m');
                        break;
                    case 'day':
                        $replace = date('d');
                        break;
                    case 'id':
                        $replace = str_pad($next_id, $left_pad, '0', STR_PAD_LEFT);
                        break;
                    default:
                        $replace = '';
                }

                $identifier_format = str_replace('{{{' . $var . '}}}', $replace, $identifier_format);
            }
        }

        return $identifier_format;
    }
    
    public function repoGroupquery(string $id): Group    {
        $query = $this->select()->where(['id' => $id]);
        return  $query->fetchOne();        
    }
    
    /**
     * @param $invoice_group_id
     */
    public function set_next_invoice_number(int $id)
    {
        $result = $this->repoGroupquery((string)$id);
        $next_id = $result->getNext_id() + 1;
        $result->setNext_id($next_id);            
    }
}