<?php

declare(strict_types=1);

namespace App\Command\Fixture;

use App\Blog\Entity\Comment;
use App\Blog\Entity\Post;
use App\Blog\Entity\Tag;
use App\Blog\Tag\TagRepository;
use App\User\User;
use Cycle\ORM\EntityManager;
use DateTimeImmutable;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;
use Yiisoft\Data\Cycle\Writer\EntityWriter;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Cycle\Command\CycleDependencyProxy;

final class AddCommand extends Command
{
    protected static $defaultName = 'fixture/add';

    private Generator $faker;
    /** @var User[] */
    private array $users = [];
    /** @var Tag[] */
    private array $tags = [];

    private const DEFAULT_COUNT = 10;

    public function __construct(
        private CycleDependencyProxy $promise,
        private EntityManager $entityManager,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
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

        $count = (int) $input->getArgument('count');
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
        } catch (Throwable $t) {
            $io->error($t->getMessage());
            $this->logger->error($t->getMessage(), ['exception' => $t]);

            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
        $io->success('Done');

        return ExitCode::OK;
    }

    private function saveEntities(): void
    {
        (new EntityWriter($this->entityManager))->write($this->users);
    }

    private function addUsers(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $login = $this->faker->unique()->firstName;
            $user = new User($login, $login);
            $this->users[] = $user;
        }
    }

    private function addTags(int $count): void
    {
        /** @var TagRepository $tagRepository */
        $tagRepository = $this->promise
            ->getORM()
            ->getRepository(Tag::class);
        $this->tags = [];
        $tagWords = [];
        for ($i = 0, $fails = 0; $i < $count; $i++) {
            $word = $this->faker->word();
            if (in_array($word, $tagWords, true)) {
                $i--;
                $fails++;
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

    private function addPosts(int $count): void
    {
        if (empty($this->users)) {
            throw new Exception('No users');
        }
        for ($i = 0; $i < $count; $i++) {
            /** @var User $postUser */
            $postUser = $this->users[array_rand($this->users)];
            $post = new Post($this->faker->text(64), $this->faker->realText(random_int(1000, 4000)));
            $postUser->addPost($post);
            $public = random_int(0, 2) > 0;
            $post->setPublic($public);
            if ($public) {
                $post->setPublishedAt(new DateTimeImmutable(date('r', random_int(strtotime('-2 years'), time()))));
            }
            // link tags
            $postTags = (array) array_rand($this->tags, random_int(1, count($this->tags)));
            foreach ($postTags as $tagId) {
                $tag = $this->tags[$tagId];
                $post->addTag($tag);
                // todo: uncomment when issue is resolved https://github.com/cycle/orm/issues/70
                // $tag->addPost($post);
            }
            // add comments
            $commentsCount = random_int(0, $count);
            for ($j = 0; $j <= $commentsCount; $j++) {
                $comment = new Comment($this->faker->realText(random_int(100, 500)));
                $commentPublic = random_int(0, 3) > 0;
                $comment->setPublic($commentPublic);
                if ($commentPublic) {
                    $comment->setPublishedAt(new DateTimeImmutable(date('r', random_int(strtotime('-1 years'), time()))));
                }
                $commentUser = $this->users[array_rand($this->users)];
                $commentUser->addComment($comment);
                $comment->setPost($post);
            }
        }
    }
}
