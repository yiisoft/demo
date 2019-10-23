<?php
namespace App;

use Psr\Http\Message\ResponseFactoryInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\View\ViewContextInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Web\User\User;

abstract class Controller implements ViewContextInterface
{
    protected $responseFactory;
    private $aliases;
    private $view;
    private $layout;
    protected $user;

    public function __construct(ResponseFactoryInterface $responseFactory, Aliases $aliases, WebView $view, User $user)
    {
        $this->responseFactory = $responseFactory;
        $this->aliases = $aliases;
        $this->view = $view;
        $this->layout = $aliases->get('@views') . '/layout/main';
        $this->user = $user;
    }

    protected function render(string $view, array $parameters = []): string
    {
        $content = $this->view->render($view, $parameters, $this);
        return $this->renderContent($content);
    }

    private function renderContent($content): string
    {
        $user = $this->user->getIdentity();

        $layout = $this->findLayoutFile($this->layout);
        if ($layout !== null) {
            return $this->view->renderFile(
                $layout,
                [
                    'content' => $content,
                    'user' => $user,
                ],
                $this
            );
        }

        return $content;
    }

    public function getViewPath(): string
    {
        return $this->aliases->get('@views') . '/' . $this->getId();
    }

    private function findLayoutFile(?string $file): ?string
    {
        if ($file === null) {
            return null;
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }

        return $file . '.' . $this->view->getDefaultExtension();
    }

    abstract protected function getId(): string;
}
