<?php

declare(strict_types=1);

namespace App\Auth\Controller;

use App\Auth\AuthService;
use App\Auth\Form\SignupForm;
use App\Service\WebControllerService;
use App\User\UserLoginException;
use App\User\UserPasswordException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Method;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
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
        AuthService $authService,
        ServerRequestInterface $request,
        TranslatorInterface $translator,
        ValidatorInterface $validator
    ): ResponseInterface {
        if (!$authService->isGuest()) {
            return $this->redirectToMain();
        }

        $body = $request->getParsedBody();

        $signupForm = new SignupForm($translator);

        if (
            $request->getMethod() === Method::POST
            && $signupForm->load(is_array($body) ? $body : [])
            && $validator
                ->validate($signupForm)
                ->isValid()
        ) {
            try {
                $authService->signup($signupForm->getLogin(), $signupForm->getPassword());
                return $this->redirectToMain();
            } catch (UserLoginException $exception) {
                $signupForm->getFormErrors()->addError('login', $exception->getMessage());
            } catch (UserPasswordException $exception) {
                $signupForm->getFormErrors()->addError('password', $exception->getMessage());
            }
        }

        return $this->viewRenderer->render('signup', ['formModel' => $signupForm]);
    }

    private function redirectToMain(): ResponseInterface
    {
        return $this->webService->getRedirectResponse('site/index');
    }
}
