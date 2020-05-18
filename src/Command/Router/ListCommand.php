<?php

declare(strict_types=1);

namespace App\Command\Router;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Yii\Console\ExitCode;

class ListCommand extends Command
{
    private UrlMatcherInterface $urlMatcher;

    protected static $defaultName = 'router/list';

    public function __construct(UrlMatcherInterface $urlMatcher)
    {
        $this->urlMatcher = $urlMatcher;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('List all registered routes')
            ->setHelp('This command displays a list of registered routes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);
        $routes = $this->urlMatcher->getRouteCollection()->getRoutes();
        uasort(
            $routes,
            static function ($a, $b) {
                return ($a->getHost() <=> $b->getHost()) ?: ($a->getName() <=> $b->getName());
            }
        );
        $table->setHeaders(['Host', 'Methods', 'Name', 'Pattern', 'Defaults']);
        foreach ($routes as $route) {
            $table->addRow(
                [
                    $route->getHost(),
                    implode(',', $route->getMethods()),
                    $route->getName(),
                    $route->getPattern(),
                    implode(',', $route->getDefaults())
                ]
            );
            if (next($routes) !== false) {
                $table->addRow(new TableSeparator());
            }
        }

        $table->render();
        return ExitCode::OK;
    }
}
