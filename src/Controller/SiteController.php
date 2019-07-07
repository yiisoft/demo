<?php
namespace App\Controller;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use yii\base\Aliases;
use Yiisoft\View\ViewContextInterface;
use Yiisoft\View\WebView;

class SiteController implements ViewContextInterface
{
    private $responseFactory;
    private $aliases;
    private $view;
    private $layout;

    public function __construct(ResponseFactoryInterface $responseFactory, Aliases $aliases, WebView $view)
    {
        $this->responseFactory = $responseFactory;
        $this->aliases = $aliases;
        $this->view = $view;
        $this->layout = $aliases->get('@views') . '/layout/main.php';
    }

    public function index(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        $output = $this->render('index');

        $response->getBody()->write($output);
        return $response;
    }

    public function testParameter(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write('You are at test with param ' . $id);
        return $response;
    }

    private function render(string $view, array $parameters = []): string
    {
        $content = $this->view->render($view, $parameters, $this);
        return $this->renderContent($content);
    }

    private function renderContent($content): string
    {
        if ($this->layout !== null) {
            return $this->view->renderFile($this->layout, ['content' => $content], $this);
        }

        return $content;
    }

    /**
     * @return string the view path that may be prefixed to a relative view name.
     */
    public function getViewPath(): string
    {
        return $this->aliases->get('@views') . '/site';
    }
}
