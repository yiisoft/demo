<?php

namespace App\Factory;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\Theme;
use Yiisoft\View\WebView;
use Yiisoft\Widget\WidgetFactory;

class ViewFactory
{
    public function __invoke(ContainerInterface $container)
    {
        WidgetFactory::initialize($container);

        $webView = new WebView(
            $container->get(Aliases::class)->get('@views'),
            $container->get(Theme::class),
            $container->get(EventDispatcherInterface::class),
            $container->get(LoggerInterface::class)
        );

        /**
         * Passes {{@see UrlGeneratorInterface}} {{@see AssetManager}} to view files.
         * It will be available as $urlGenerator, $assetManager in view or layout.
         */
        $webView->setDefaultParameters(
            [
                'assetManager' => $container->get(AssetManager::class),
                'urlGenerator' => $container->get(UrlGeneratorInterface::class),
            ]
        );

        return $webView;
    }
}
