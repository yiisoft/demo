<?php

namespace App\Factory;

use Psr\Container\ContainerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Log\FileRotatorInterface;
use Yiisoft\Log\FileTarget;
use Yiisoft\Log\Logger;
use Psr\Log\LogLevel;

class LoggerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $aliases = $container->get(Aliases::class);
        $fileRotator = $container->get(FileRotatorInterface::class);
        $fileTarget = new FileTarget(
            $aliases->get('@runtime/logs/app.log'),
            $fileRotator
        );
        $fileTarget->setLevels([LogLevel::EMERGENCY, LogLevel::ERROR, LogLevel::WARNING, LogLevel::INFO, LogLevel::DEBUG]);

        return new Logger([
            'file' => $fileTarget,
        ]);
    }
}
