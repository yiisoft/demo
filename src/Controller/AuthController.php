<?php

namespace App\Controller;

use App\ViewRenderer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\Web\User\User;

class AuthController
{
    private ResponseFactoryInterface $responseFactory;
    private LoggerInterface $logger;
    private UrlGeneratorInterface $urlGenerator;
    private ViewRenderer $viewRenderer;
    private User $user;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ViewRenderer $viewRenderer,
        LoggerInterface $logger,
        UrlGeneratorInterface $urlGenerator,
        User $user
    ) {
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
        $this->urlGenerator = $urlGenerator;
        $this->viewRenderer = $viewRenderer->withControllerName('auth');
        $this->user = $user;
    }

    public function login(
        ServerRequestInterface $request,
        IdentityRepositoryInterface $identityRepository
    ): ResponseInterface {
        $body = $request->getParsedBody();
        $error = null;

        if ($request->getMethod() === Method::POST) {
            try {
                foreach (['login', 'password'] as $name) {
                    if (empty($body[$name])) {
                        throw new \InvalidArgumentException(ucfirst($name) . ' is required');
                    }
                }

                /** @var \App\Entity\User $identity */
                $identity = $identityRepository->findByLogin($body['login']);
                if ($identity === null) {
                    throw new \InvalidArgumentException('No such user');
                }

                if (!$identity->validatePassword($body['password'])) {
                    throw new \InvalidArgumentException('Invalid password');
                }

                if ($this->user->login($identity)) {
                    return $this->responseFactory
                        ->createResponse(302)
                        ->withHeader(
                            'Location',
                            $this->urlGenerator->generate('site/index')
                        );
                }

                throw new \InvalidArgumentException('Unable to login');
            } catch (\Throwable $e) {
                $this->logger->error($e);
                $error = $e->getMessage();
            }
        }

        return $this->viewRenderer->render(
            'login',
            [
                'csrf' => $request->getAttribute('csrf_token'),
                'body' => $body,
                'error' => $error,
            ]
        );
    }

    public function logout(): ResponseInterface
    {
        $this->user->logout();

        return $this->responseFactory
            ->createResponse(302)
            ->withHeader(
                'Location',
                $this->urlGenerator->generate('site/index')
            );
    }
}
