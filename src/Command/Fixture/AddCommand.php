<?php

namespace App\Command\Fixture;

use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use App\Blog\Entity\Tag;
use App\Entity\User;
use App\Blog\Tag\TagRepository;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Yii\Console\ExitCode;

class AddCommand extends Command
{
    protected static $defaultName = 'fixture/add';

    private ORMInterface $orm;
    private Generator $faker;
    /** @var User[] */
    private array $users = [];
    /** @var Tag[] */
    private array $tags = [];

    private const DEFAULT_COUNT = 10;

    public function __construct(ORMInterface $orm)
    {
        parent::__construct();
        $this->orm = $orm;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Add fixtures')
            ->setHelp('This command adds random content')
            ->addArgument('count', InputArgument::OPTIONAL, 'Count', self::DEFAULT_COUNT);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $count = (int)$input->getArgument('count');
        // get faker
        if (!class_exists(Factory::class)) {
            $io->error('Faker should be installed. Run `composer install --dev`');
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $this->faker = Factory::create();

        try {
            $this->addUsers($count);
            $this->addTags($count);
            $this->addPosts($count);

            $this->saveEntities();
        } catch (\Throwable $t) {
            $io->error($t->getMessage());
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
        $io->success('Done');
        return ExitCode::OK;
    }

    private function saveEntities():void
    {
        $transaction = new Transaction($this->orm);
        foreach ($this->users as $user) {
            $transaction->persist($user);
        }
        $transaction->run();
    }

    private function addUsers(int $count = self::DEFAULT_COUNT): void
    {
        for ($i = 0; $i <= $count; ++$i) {
            $user = new User();
            $user->setLogin($login = $this->faker->firstName . rand(0, 9999));
            $user->setPassword($login);
            $this->users[] = $user;
        }
    }

    private function addTags(int $count = self::DEFAULT_COUNT): void
    {
        /** @var TagRepository $tagRepository */
        $tagRepository = $this->orm->getRepository(Tag::class);
        $this->tags = [];
        $tagWords = [];
        for ($i = 0, $fails = 0; $i <= $count; ++$i) {
            $word = $this->faker->word();
            if (in_array($word, $tagWords, true)) {
                --$i;
                ++$fails;
                if ($fails >= $count) {
                    break;
                }
                continue;
            }
            $tagWords[] = $word;
            $tag = $tagRepository->getOrCreate($word);
            $this->tags[] = $tag;
        }
    }

    private function addPosts(int $count = self::DEFAULT_COUNT): void
    {
        if (count($this->users) === 0) {
            throw new \Exception('No users');
        }
        for ($i = 0; $i <= $count; ++$i) {
            /** @var User $postUser */
            $postUser = $this->users[array_rand($this->users)];
            $post = new Post();
            $post->setUser($postUser);
            $postUser->addPost($post);
            $post->setTitle($this->faker->text(64));
            $post->setContent($this->faker->realText(4000));
            $public = rand(0, 2) > 0;
            $post->setPublic($public);
            if ($public) {
                $post->setPublishedAt(new \DateTimeImmutable(date('r', rand(time(), strtotime('-2 years')))));
            }
            // link tags
            $postTags = (array)array_rand($this->tags, rand(1, count($this->tags)));
            foreach ($postTags as $tagId) {
                $post->addTag($this->tags[$tagId]);
            }
            // add comments
            $commentsCount = rand(0, $count);
            for ($j = 0; $j <= $commentsCount; ++$j) {
                $comment = new Comment();
                $comment->setContent($this->faker->realText(500));
                $commentPublic = rand(0, 3) > 0;
                $comment->setPublic($commentPublic);
                if ($commentPublic) {
                    $comment->setPublishedAt(new \DateTimeImmutable(date('r', rand(time(), strtotime('-1 years')))));
                }
                $commentUser = $this->users[array_rand($this->users)];
                $comment->setUser($commentUser);
                $post->addComment($comment);
            }
        }
    }
}
