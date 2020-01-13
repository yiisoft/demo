<?php

namespace App\Command\Fixture;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\TagRepository;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Yii\Console\ExitCode;

class AddCommand extends Command
{
    private ORMInterface $orm;

    protected static $defaultName = 'fixture/add';

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
            ->addArgument('count', InputArgument::OPTIONAL, 'Count', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $count = $input->getArgument('count');
        // get faker
        if (!class_exists(Factory::class)) {
            $io->error('Faker should be installed. Run `composer install --dev`');
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $faker = Factory::create();

        // users
        $users = [];
        for ($i = 0; $i <= $count; ++$i) {
            $user = new User();
            $user->setLogin($faker->firstName . rand(0, 9999));
            $user->setPassword($faker->password);
            $users[] = $user;
        }
        // tags
        /** @var TagRepository $tagRepository */
        $tagRepository = $this->orm->getRepository(Tag::class);
        $tags = [];
        $tagWords = [];
        for ($i = 0, $fails = 0; $i <= $count; ++$i) {
            $word = $faker->word();
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
            $tags[] = $tag;
        }
        // posts
        for ($i = 0; $i <= $count; ++$i) {
            /** @var User $postUser */
            $postUser = $users[array_rand($users)];
            $post = new Post();
            $post->setUser($postUser);
            $postUser->addPost($post);
            $post->setTitle($faker->text(64));
            $post->setContent($faker->realText(4000));
            $public = rand(0, 2) > 0;
            $post->setPublic($public);
            if ($public) {
                $post->setPublishedAt(new \DateTimeImmutable(date('r', rand(time(), strtotime('-2 years')))));
            }
            // tags
            $postTags = (array)array_rand($tags, rand(1, count($tags)));
            foreach ($postTags as $tagId) {
                $post->addTag($tags[$tagId]);
            }
            // comments
            $commentsCount = rand(0, $count);
            for ($j = 0; $j <= $commentsCount; ++$j) {
                $comment = new Comment();
                $comment->setContent($faker->realText(500));
                $commentPublic = rand(0, 3) > 0;
                $comment->setPublic($commentPublic);
                if ($commentPublic) {
                    $comment->setPublishedAt(new \DateTimeImmutable(date('r', rand(time(), strtotime('-1 years')))));
                }
                $commentUser = $users[array_rand($users)];
                $comment->setUser($commentUser);
                $post->addComment($comment);
            }
        }

        try {
            $transaction = new Transaction($this->orm);
            foreach ($users as $user) {
                $transaction->persist($user);
            }
            $transaction->run();
            $io->success('Done');
        } catch (\Throwable $t) {
            $io->error($t->getMessage());
            return $t->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }
}
