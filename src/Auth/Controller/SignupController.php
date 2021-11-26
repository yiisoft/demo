<?php

declare(strict_types=1);

namespace App\Auth\Controller;

use App\Auth\AuthService;
use App\Service\WebControllerService;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;

final class SignupController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;

    public function __construct(ViewRenderer $viewRenderer, WebControllerService $webService)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('signup');
        $this->webService = $webService;
    }

    public function signup(
        ServerRequestInterface $request,
        LoggerInterface $logger,
        AuthService $authService,
    ): ResponseInterface {
        if (!$authService->isGuest()) {
            return $this->redirectToMain();
        }

        $body = $request->getParsedBody();
        $error = null;

        if ($request->getMethod() === Method::POST) {
            try {
                foreach (['login', 'password'] as $name) {
                    if (empty($body[$name])) {
                        throw new InvalidArgumentException(ucfirst($name) . ' is required.');
                    }
                }

                $authService->signup($body['login'], $body['password']);
                return $this->redirectToMain();
            } catch (Throwable $e) {
                $logger->error($e);
                $error = $e->getMessage();
            }
        }

        return $this->viewRenderer->render('signup', [
            'body' => $body,
            'error' => $error,
        ]);
    }

    private function redirectToMain(): ResponseInterface
    {
        return $this->webService->getRedirectResponse('site/index');
    }
}
