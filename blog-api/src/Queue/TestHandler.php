<?php

namespace App\Queue;

use Psr\Log\LoggerInterface;
use Yiisoft\Json\Json;
use Yiisoft\Yii\Queue\Message\Message;

class TestHandler
{
    public const NAME = 'test-handler';
    public const CHANNEL = 'channel-1';

    public function __construct(private LoggerInterface $logger)
    {
    }

    public function handle(Message $message): void
    {
        #StreamTarget doesn't log to stdout with queue/listen ??
        echo Json::encode($message->getData()) . PHP_EOL . 'object_id: ' . spl_object_id($message) . PHP_EOL;
        $this->logger->info('test', [
            'data' => $message->getData(),
        ]);
    }
}
