<?php

namespace App\Blog\Tag;

use App\Blog\Entity\Post;
use App\Blog\Entity\PostTag;
use App\Blog\Entity\Tag;
use App\Blog\Post\PostRepository;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Select\Repository;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Yii\Cycle\DataReader\SelectDataReader;

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
            $tag = new Tag($label);
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
     * @return SelectDataReader Collection of Array('label' => 'Tag Label', 'count' => '8')
     */
    public function getTagMentions(int $limit = 0): DataReaderInterface
    {
        /** @var Repository $postTagRepo */
        $postTagRepo = $this->orm->getRepository(PostTag::class);
        /** @var PostRepository $postRepo */
        $postRepo = $this->orm->getRepository(Post::class);

        $case1 = $postTagRepo
            ->select()
            ->buildQuery()
            ->columns(['t.label', 'count(*) count'])
            ->innerJoin('post', 'p')->on('p.id', 'postTag.post_id')
                                    ->onWhere(['p.public' => true, 'deleted_at' => null])
            ->innerJoin('tag', 't')->on('t.id', 'postTag.tag_id')
            ->groupBy('tag_id');

        $case2 = $this
            ->select()
            ->with('posts')
            ->buildQuery()
            ->columns(['label', 'count(*) count'])
            ->groupBy('tag.id');

        $case3 = $this
            ->select()
            ->groupBy('posts.@.tag_id') // relation posts -> pivot (@) -> column
            ->buildQuery()
            ->columns(['label', 'count(*) count']);

        $case4 = $postRepo
            ->select()
            ->groupBy('tags.@.tag_id') // relation tags -> pivot (@) -> column
            ->buildQuery()
            ->columns(['label', 'count(*) count'])
            ;

        $sort = (new Sort([]))->withOrder(['count' => 'desc']);
        return (new SelectDataReader($case4))->withSort($sort)->withLimit($limit);
    }
}
