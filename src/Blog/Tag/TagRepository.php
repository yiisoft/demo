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

final class TagRepository extends Repository
{
    private ORMInterface $orm;

    public function __construct(Select $select, ORMInterface $orm)
    {
        $this->orm = $orm;
        parent::__construct($select);
    }

    public function getOrCreate(string $label): Tag
    {
        $tag = $this->findByLabel($label);
        return $tag ?? new Tag($label);
    }

    public function findByLabel(string $label): ?Tag
    {
        return $this->select()
                    ->where(['label' => $label])
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

        // For example, below are several ways to make queries
        // As a result, we should get a list of most used tags
        // All SQL-queries received on mysql database. SQL-queries may vary by driver

        /**
         * Case 1 would look like:
         *
         * SELECT `t`.`label`, count(*) `count`
         * FROM `post_tag` AS `postTag`
         * INNER JOIN `post` AS `p`
         * ON `p`.`id` = `postTag`.`post_id` AND `p`.`public` = TRUE
         * INNER JOIN `tag` AS `t`
         * ON `t`.`id` = `postTag`.`tag_id`
         * GROUP BY `t`.`label`, `tag_id`
         * ORDER BY `count` DESC
         */
        $case1 = $postTagRepo
            ->select()
            ->buildQuery()
            ->columns(['t.label', 'count(*) count'])
            ->innerJoin('post', 'p')->on('p.id', 'postTag.post_id')->onWhere(['p.public' => true])
            ->innerJoin('tag', 't')->on('t.id', 'postTag.tag_id')
            ->groupBy('t.label, tag_id');

        /**
         * Case 2 would look like:
         *
         * SELECT `label`, count(*) `count`
         * FROM `tag` AS `tag`
         * INNER JOIN `post_tag` AS `tag_posts_pivot`
         * ON `tag_posts_pivot`.`tag_id` = `tag`.`id`
         * INNER JOIN `post` AS `tag_posts`
         * ON `tag_posts`.`id` = `tag_posts_pivot`.`post_id` AND `tag_posts`.`public` = TRUE
         * GROUP BY `tag`.`label`, `tag_id`
         * ORDER BY `count` DESC
         */
        $case2 = $this
            ->select()
            ->with('posts')
            ->buildQuery()
            ->columns(['label', 'count(*) count'])
            ->groupBy('tag.label, tag_id');

        /**
         * Case 3 would look like:
         *
         * SELECT `label`, count(*) `count`
         * FROM `tag` AS `tag`
         * INNER JOIN `post_tag` AS `tag_posts_pivot`
         * ON `tag_posts_pivot`.`tag_id` = `tag`.`id`
         * INNER JOIN `post` AS `tag_posts`
         * ON `tag_posts`.`id` = `tag_posts_pivot`.`post_id` AND `tag_posts`.`public` = TRUE
         * GROUP BY `tag_posts_pivot`.`tag_id`, `tag`.`label`
         * ORDER BY `count` DESC
         */
        $case3 = $this
            ->select()
            ->groupBy('posts.@.tag_id') // relation posts -> pivot (@) -> column
            ->groupBy('label')
            ->buildQuery()
            ->columns(['label', 'count(*) count']);

        /**
         * Case 4 would look like:
         *
         * SELECT `label`, count(*) `count`
         * FROM `post` AS `post`
         * INNER JOIN `post_tag` AS `post_tags_pivot`
         * ON `post_tags_pivot`.`post_id` = `post`.`id`
         * INNER JOIN `tag` AS `post_tags`
         * ON `post_tags`.`id` = `post_tags_pivot`.`tag_id`
         * WHERE `post`.`public` = TRUE
         * GROUP BY `post_tags_pivot`.`tag_id`, `tag`.`label`
         */
        $case4 = $postRepo
            ->select()
            ->groupBy('tags.@.tag_id') // relation tags -> pivot (@) -> column
            ->groupBy('tags.label')
            ->buildQuery()
            ->columns(['label', 'count(*) count']);

        $sort = (new Sort([]))->withOrder(['count' => 'desc']);
        return (new SelectDataReader($case3))->withSort($sort)->withLimit($limit);
    }
}
