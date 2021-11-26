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

final class AuthController
{
    private WebControllerService $webService;
    private ViewRenderer $viewRenderer;
    private AuthService $authService;

    public function __construct(ViewRenderer $viewRenderer, AuthService $authService, WebControllerService $webService)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('auth');
        $this->authService = $authService;
        $this->webService = $webService;
    }

    public function login(ServerRequestInterface $request, LoggerInterface $logger): ResponseInterface
    {
        if (!$this->authService->isGuest()) {
            return $this->redirectToMain();
        }

        $body = $request->getParsedBody();
        $error = null;

        if ($request->getMethod() === Method::POST) {
            try {
                foreach (['login', 'password'] as $name) {
                    if (empty($body[$name])) {
                        throw new InvalidArgumentException(ucfirst($name) . ' is required');
                    }
                }

                if ($this->authService->login($body['login'], $body['password'], isset($body['remember']))) {
                    return $this->redirectToMain();
                }

                throw new InvalidArgumentException('Unable to login.');
            } catch (Throwable $e) {
                $logger->error($e);
                $error = $e->getMessage();
            }
        }

        return $this->viewRenderer->render('login', [
            'body' => $body,
            'error' => $error,
        ]);
    }

    public function logout(): ResponseInterface
    {
        $this->authService->logout();

        return $this->redirectToMain();
    }

    private function redirectToMain(): ResponseInterface
    {
        return $this->webService->getRedirectResponse('site/index');
    }
}
