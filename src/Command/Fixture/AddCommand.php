<?php

namespace App\Command\Fixture;

use App\Entity\Post;
use App\Entity\User;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Yii\Console\ExitCode;

class AddCommand extends Command
{
    private $orm;

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
        $faker = \Faker\Factory::create();

        // users
        $users = [];
        for ($i = 0; $i <= $count; ++$i) {
            $user = new User();
            $user->setLogin($faker->firstName . rand(0, 9999));
            $user->setPassword($faker->password);
            $users[] = $user;
        }
        // posts
        for ($i = 0; $i <= $count; ++$i) {
            /** @var User $user */
            $user = $users[array_rand($users)];
            $post = new Post($user);
            $user->getPosts()->add($post);
            $post->setTitle($faker->text(250));
            $post->setContent($faker->realText(4000));
            $post->setPublic((bool)rand(0, 1));
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
