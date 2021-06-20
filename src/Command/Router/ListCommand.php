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

class ListCommand extends Command
{
    private RouteCollectionInterface $routeCollection;

    protected static $defaultName = 'router/list';

    public function __construct(RouteCollectionInterface $routeCollection)
    {
        $this->routeCollection = $routeCollection;
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
            static function ($a, $b) {
                return ($a->getParameter(Route::HOST) <=> $b->getParameter(Route::HOST)) ?: ($a->getParameter(Route::NAME, $a->getDefaultName()) <=> $b->getParameter(Route::NAME, $b->getDefaultName()));
            }
        );
        $table->setHeaders(['Host', 'Methods', 'Name', 'Pattern', 'Defaults']);
        foreach ($routes as $route) {
            $table->addRow(
                [
                    $route->getParameter(Route::HOST),
                    implode(',', $route->getParameter(Route::METHODS)),
                    $route->getParameter(Route::NAME, $route->getDefaultName()),
                    $route->getParameter(Route::PATTERN),
                    implode(',', $route->getParameter(Route::DEFAULTS)),
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
