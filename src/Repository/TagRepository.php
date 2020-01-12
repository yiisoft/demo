<?php

namespace App\Repository;

use App\Entity\PostTag;
use App\Entity\Tag;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Select\Repository;
use Spiral\Database\Injection\Fragment;

class TagRepository extends Repository
{
    private ORMInterface $orm;

    public function __construct(ORMInterface $orm, $role = Tag::class)
    {
        parent::__construct(new Select($orm, $role));
        $this->orm = $orm;
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

    /**
     * @param int $limit
     * @return array Array of Array('label' => 'Tag Label', 'count' => '8')
     */
    public function getTagMentions(int $limit = 0): array
    {
        /** @var Repository $postTagRepo */
        $postTagRepo = $this->orm->getRepository(PostTag::class);
        $data = $postTagRepo
            ->select()
            ->buildQuery()
            ->columns(['t.label', 'count(*) count'])
            ->innerJoin('post', 'p')->on('p.id', 'postTag.post_id')
                                    ->onWhere('p.public', true)
            ->innerJoin('tag', 't')->on('t.id', 'postTag.tag_id')
            ->orderBy(new Fragment('count'), 'DESC')
            ->groupBy('tag_id')
            ->limit($limit)
            ->fetchAll();

        return $data;
    }
}
