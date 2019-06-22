<?php
namespace App\Factory;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\View\Theme;
use Yiisoft\View\WebView;

class ViewFactory
{
    private $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function __invoke(ContainerInterface $container)
    {
        $theme = $container->get(Theme::class);
        $logger = $container->get(LoggerInterface::class);
        $eventDispatcher = $container->get(EventDispatcherInterface::class);
        return new WebView($this->basePath, $theme, $eventDispatcher, $logger);
    }

    public static function __set_state(array $state): self
    {
        return new self($state['basePath']);
    }
}
