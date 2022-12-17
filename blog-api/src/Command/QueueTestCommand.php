<?php

namespace App\Command;

use App\Queue\TestHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Json\Json;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Queue\Message\Message;
use Yiisoft\Yii\Queue\QueueFactoryInterface;

class QueueTestCommand extends Command
{
    public static $defaultName = 'queue-test';
    protected static $defaultDescription = 'yii3 AMQP queue test';

    private QueueFactoryInterface $queueFactory;

    public function __construct(
        QueueFactoryInterface $queueFactory
    ) {
        $this->queueFactory = $queueFactory;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDefinition(
            new InputDefinition([
                new InputOption('--some_id', '-s', InputOption::VALUE_REQUIRED, 'some_id'),
            ])
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queueMessage = new Message(TestHandler::NAME, ['some_id' => $input->getOption('some_id'), 'time' => time()]);
        $channel = $this->queueFactory->get(TestHandler::CHANNEL);
        $message = $channel->push($queueMessage);
        $output->writeln(Json::encode($message->getData()) . ' [' . $message->getId() . '] ~> ' . $channel->getChannelName());
        return ExitCode::OK;
    }
}
