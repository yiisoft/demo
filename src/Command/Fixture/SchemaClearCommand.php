<?php

declare(strict_types=1);

namespace App\Command\Fixture;

use App\Blog\Entity\PostTag;
use App\Blog\Entity\Tag;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Yii\Cycle\Command\CycleDependencyProxy;
use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use App\User\User;
use function count;


final class SchemaClearCommand extends Command
{
    protected static $defaultName = 'fixture/schema/clear';

    private CycleDependencyProxy $promise;


    public function __construct(
        CycleDependencyProxy $promise,
    ) {
        $this->promise = $promise;
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setDescription('Clear database from fixtures')
            ->setHelp('This command delete all tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->promise
            ->getDatabaseProvider()
            ->database()
            ->delete('post')
            ->run();

        $this->promise
            ->getDatabaseProvider()
            ->database()
            ->delete('post_tag')
            ->run();

        $this->promise
            ->getDatabaseProvider()
            ->database()
            ->delete('tag')
            ->run();

        $this->promise
            ->getDatabaseProvider()
            ->database()
            ->delete('user')
            ->run();

        $this->promise
            ->getDatabaseProvider()
            ->database()
            ->delete('comment')
            ->run();

        return count($this->promise
                ->getORM()
                ->getRepository(Post::class)
                ->findAll()) +
            count($this->promise
                ->getORM()
                ->getRepository(PostTag::class)
                ->findAll())+
            count($this->promise
                ->getORM()
                ->getRepository(Tag::class)
                ->findAll())+
            count($this->promise
                ->getORM()
                ->getRepository(User::class)
                ->findAll())+
            count($this->promise
                ->getORM()
                ->getRepository(Comment::class)
                ->findAll());

    }

}
