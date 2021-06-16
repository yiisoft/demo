<?php

declare(strict_types=1);

namespace App\Invoice\Client;

use App\Invoice\Entity\Client;
use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\Data\Reader\EntityReader;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class ClientRepository extends Select\Repository
{
    private EntityWriter $entityWriter;

    public function __construct(Select $select, EntityWriter $entityWriter)
    {
        $this->entityWriter = $entityWriter;
        parent::__construct($select);
    }

    /**
     * Get clients without filter
     *
     * @psalm-return DataReaderInterface<int, Client>
     */
    public function findAllPreloaded(): DataReaderInterface
    {
        $query = $this->select();
        return $this->prepareDataReader($query);
    }

    /**
     * @throws Throwable
     */
    public function save(Client $client): void
    {
        $this->entityWriter->write([$client]);
    }
    
    /**
     * @throws Throwable
     */
    public function delete(Client $client): void
    {
        $this->entityWriter->delete([$client]);
    }

    private function prepareDataReader($query): EntityReader
    {
        return (new EntityReader($query))->withSort(
            Sort::only(['id', 'client_active', 'client_date_created', 'client_date_modified', 'user_id'])
                ->withOrder(['client_date_created' => 'desc'])
        );
    }
    
    public function repoClientquery(string $client_id): Client
    {
        $query = $this
            ->select()
            ->where(['id' => $client_id]);
        return  $query->fetchOne();        
    }
}
