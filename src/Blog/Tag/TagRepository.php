<?php

namespace App\Blog\Tag;

use App\Blog\Entity\PostTag;
use App\Blog\Entity\Tag;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Select\Repository;
use Spiral\Database\Injection\Fragment;

class TagRepository extends Repository
{
    private ORMInterface $orm;

    public function __construct(Select $select, ORMInterface $orm)
    {
        parent::__construct($select);
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

        $case1 = $postTagRepo
            ->select()
            ->buildQuery()
            ->columns(['t.label', 'count(*) count'])
            ->innerJoin('post', 'p')->on('p.id', 'postTag.post_id')
                                    ->onWhere(['p.public' => true, 'deleted_at' => null])
            ->innerJoin('tag', 't')->on('t.id', 'postTag.tag_id')
            ->orderBy(new Fragment('count'), 'DESC')
            ->groupBy('tag_id')
            ->limit($limit);

        $case2 = $this
            ->select()
            ->with('posts')
            ->buildQuery()
            ->columns(['label', 'count(*) count'])
            ->orderBy('count', 'DESC')
            ->groupBy('tag.id')
            ->limit($limit);

        // best way
        $case3 = $this
            ->select()
            ->groupBy('posts.@.tag_id') // relation posts -> pivot (@) -> column
            ->buildQuery()
            ->columns(['label', 'count(*) count'])
            ->orderBy('count', 'DESC')
            ->limit($limit);

        return $case3->fetchAll();
    }
}
