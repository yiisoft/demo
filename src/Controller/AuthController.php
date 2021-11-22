<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\LoginForm;
use App\User\User;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

class AuthController
{
    private ResponseFactoryInterface $responseFactory;
    private UrlGeneratorInterface $urlGenerator;
    private ViewRenderer $viewRenderer;
    private CurrentUser $currentUser;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ViewRenderer $viewRenderer,
        UrlGeneratorInterface $urlGenerator,
        CurrentUser $currentUser
    ) {
        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
        $this->viewRenderer = $viewRenderer->withControllerName('auth');
        $this->currentUser = $currentUser;
    }

    public function login(
        IdentityRepositoryInterface $identityRepository,
        LoginForm $loginForm,
        ServerRequestInterface $request,
        TranslatorInterface $translator,
        ValidatorInterface $validator
    ): ResponseInterface {
        if (!$this->currentUser->isGuest()) {
            return $this->redirectToMain();
        }

        /** @var array */
        $body = $request->getParsedBody();
        $error = null;

        if (
            $request->getMethod() === Method::POST
            && $loginForm->load($body)
            && $validator->validate($loginForm)->isValid()
        ) {
            /** @var User $identity */
            $identity = $identityRepository->findByLogin($loginForm->getAttributeValue('login'));

            if ($identity === null || !$identity->validatePassword($loginForm->getAttributeValue('password'))) {
                $loginForm->getFormErrors()->addError('password', $translator->translate('Invalid login or password'));
            } elseif ($this->currentUser->login($identity)) {
                return $this->redirectToMain();
            }
        }

        return $this->viewRenderer->render(
            'login',
            [
                'body' => $body,
                'formModel' => $loginForm,
                'error' => $error,
            ]
        );
    }

    public function logout(): ResponseInterface
    {
        $this->currentUser->logout();

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
