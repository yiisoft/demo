<?php

declare(strict_types=1);

namespace App\Auth\Controller;

use App\Auth\AuthService;
use App\Auth\Form\LoginForm;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\Login\Cookie\CookieLogin;
use Yiisoft\User\Login\Cookie\CookieLoginIdentityInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class AuthController
{
    public function __construct(
        private AuthService $authService,
        private WebControllerService $webService,
        private ViewRenderer $viewRenderer,
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('auth');
    }

    public function login(
        ServerRequestInterface $request,
        TranslatorInterface $translator,
        FormHydrator $formHydrator,
        CookieLogin $cookieLogin
    ): ResponseInterface {
        if (!$this->authService->isGuest()) {
            return $this->redirectToMain();
        }

        $body = $request->getParsedBody();
        $loginForm = new LoginForm($this->authService, $translator);

        if (
            $request->getMethod() === Method::POST
            && $formHydrator->populate($loginForm, $body)
            && $loginForm->isValid()
        ) {
            $identity = $this->authService->getIdentity();

            if ($identity instanceof CookieLoginIdentityInterface && $loginForm->getPropertyValue('rememberMe')) {
                return $cookieLogin->addCookie($identity, $this->redirectToMain());
            }

            return $this->redirectToMain();
        }

        return $this->viewRenderer->render('login', ['formModel' => $loginForm]);
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
