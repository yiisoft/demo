<?php

declare(strict_types=1);

namespace App\Command\Route;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Router\Route;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Yii\Console\ExitCode;

class ListCommand extends Command
{
    private UrlMatcherInterface $urlMatcher;

    protected static $defaultName = 'route/list';

    public function __construct(UrlMatcherInterface $urlMatcher)
    {
        $this->urlMatcher = $urlMatcher;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('List all registered routes')
            ->setHelp('This command displays a list of registered routes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $io = new SymfonyStyle($input, $output);
        /** @var Route[] $routes */
        $routes = $this->urlMatcher->getRouteCollection()->getRoutes();
        $table->setHeaders(['Host', 'Methods', 'Name', 'Pattern', 'Defaults']);
        foreach ($routes as $key => $route) {
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

        try {
            $table->render();
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());
            return $exception->getCode() ?: ExitCode::UNSPECIFIED_ERROR;
        }
        return ExitCode::OK;
    }
}
