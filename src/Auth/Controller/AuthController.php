<?php

declare(strict_types=1);

namespace App\Auth\Controller;

use App\Auth\AuthService;
use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class AuthController
{
    private ResponseFactoryInterface $responseFactory;
    private LoggerInterface $logger;
    private UrlGeneratorInterface $urlGenerator;
    private ViewRenderer $viewRenderer;
    private AuthService $authService;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ViewRenderer $viewRenderer,
        LoggerInterface $logger,
        UrlGeneratorInterface $urlGenerator,
        AuthService $authService
    ) {
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
        $this->urlGenerator = $urlGenerator;
        $this->viewRenderer = $viewRenderer->withControllerName('auth');
        $this->authService = $authService;
    }

    public function login(ServerRequestInterface $request): ResponseInterface
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
                $this->logger->error($e);
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
        return $this->responseFactory->createResponse(Status::FOUND)
            ->withHeader(
                'Location',
                $this->urlGenerator->generate('site/index')
            );
    }
}
