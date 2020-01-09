<?php

namespace App\Repository;

use App\Entity\Tag;
use Cycle\ORM\Iterator;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Spiral\Database\Injection\Fragment;
use Spiral\Database\Query\SelectQuery;

class TagRepository extends Select\Repository
{
    public function __construct(ORMInterface $orm, $role = Tag::class)
    {
        parent::__construct(new Select($orm, $role));
    }

    public function getOrCreate(string $label): Tag
    {
        $tag = $this->findByLabel($label);
        if ($tag === null) {
            $tag = new Tag();
            $tag->setLabel($label);
        }
        return $tag;
    }

    public function findByLabel(string $label, array $load = []): ?Tag
    {
        return $this->select()
                    ->where(['label' => $label])
                    ->load($load)
                    ->fetchOne();
    }
}
