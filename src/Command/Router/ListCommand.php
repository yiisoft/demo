<?php

declare(strict_types=1);

namespace App\Command\Router;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollectionInterface;
use Yiisoft\Yii\Console\ExitCode;

final class ListCommand extends Command
{
    protected static $defaultName = 'router/list';

    public function __construct(private RouteCollectionInterface $routeCollection)
    {
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
        $routes = $this->routeCollection->getRoutes();
        uasort(
            $routes,
            static fn (Route $a, Route $b) => ($a->getData('host') <=> $b->getData('host')) ?: ($a->getData('name') <=> $b->getData('name'))
        );
        $table->setHeaders(['Host', 'Methods', 'Name', 'Pattern', 'Defaults']);
        foreach ($routes as $route) {
            $table->addRow(
                [
                    $route->getData('host'),
                    implode(',', $route->getData('methods')),
                    $route->getData('name'),
                    $route->getData('pattern'),
                    implode(',', $route->getData('defaults')),
                ]
            );
            if (next($routes) !== false) {
                $table->addRow(new TableSeparator());
            }
        }

        $table->setColumnMaxWidth(2, 15);
        $table->setColumnMaxWidth(3, 15);
        $table->render();
        return ExitCode::OK;
    }
}
