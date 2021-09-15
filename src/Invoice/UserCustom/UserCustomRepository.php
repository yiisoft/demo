<?php

declare(strict_types=1); 

namespace App\Invoice\UserCustom;

use App\Invoice\Entity\UserCustom;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class UserCustomRepository extends Select\Repository
{
private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get usercustoms  without filter
     *
     * @psalm-return DataReaderInterface<int,UserCustom>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }
    
    /**
     * @psalm-return DataReaderInterface<int, UserCustom>
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
    public function save(UserCustom $usercustom): void
    {
        $this->entityWriter->write([$usercustom]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(UserCustom $usercustom): void
    {
        $this->entityWriter->delete([$usercustom]);
    }
    
    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id'])
                ->withOrder(['id' => 'asc'])
        );
    }
    
    public function repoUserCustomquery(string $id): UserCustom    {
        $query = $this->select()->load('user')->where(['id' => $id]);
        return  $query->fetchOne();        
    }
}