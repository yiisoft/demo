<?php

declare(strict_types=1);

namespace App\Auth;

use Cycle\ORM\Select;
use Throwable;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

final class IdentityRepository extends Select\Repository implements IdentityRepositoryInterface
{
    public function __construct(private EntityWriter $entityWriter, Select $select)
    {
        parent::__construct($select);
    }

    /**
     * @param string $id
     *
     * @return Identity|null
     */
    public function findIdentity(string $id): ?Identity
    {
        return $this->findOne(['user_id' => $id]);
    }

    /**
     * @throws Throwable
     */
    public function save(Identity $identity): void
    {
        $this->entityWriter->write([$identity]);
    }
}
