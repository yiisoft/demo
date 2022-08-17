<?php

declare(strict_types=1);

namespace App\Auth\Controller;

use App\Auth\AuthService;
use App\Auth\Form\SignupForm;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;

final class SignupController
{
    public function __construct(private WebControllerService $webService, private ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('signup');
    }

    public function signup(
        AuthService $authService,
        ServerRequestInterface $request,
        SignupForm $signupForm
    ): ResponseInterface {
        if (!$authService->isGuest()) {
            return $this->redirectToMain();
        }

        if ($request->getMethod() === Method::POST
            && $signupForm->load($request->getParsedBody())
            && $signupForm->signup()
        ) {
            return $this->redirectToMain();
        }

        return $this->viewRenderer->render('signup', ['formModel' => $signupForm]);
    }

    private function redirectToMain(): ResponseInterface
    {
        return $this->webService->getRedirectResponse('site/index');
    }
}
