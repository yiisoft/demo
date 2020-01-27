<?php

namespace App\Factory;

use Psr\Container\ContainerInterface;
use Psr\Log\LogLevel;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Log\Logger;
use Yiisoft\Log\Target\File\FileRotatorInterface;
use Yiisoft\Log\Target\File\FileTarget;

class LoggerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $fileTarget = new FileTarget(
            $container->get(Aliases::class)->get('@runtime/logs/app.log'),
            $container->get(FileRotatorInterface::class)
        );
        $fileTarget->setLevels(
            [
                LogLevel::EMERGENCY,
                LogLevel::ERROR,
                LogLevel::WARNING,
                LogLevel::INFO,
                LogLevel::DEBUG,
            ]
        );

        return new Logger(
            [
                'file' => $fileTarget,
            ]
        );
    }
}
