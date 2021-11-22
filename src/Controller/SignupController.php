<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\LoginForm;
use App\User\User;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;
use Yiisoft\Yii\View\ViewRenderer;

final class SignupController
{
    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('signup');
    }

    public function signup(
        EntityWriter $entityWriter,
        IdentityRepositoryInterface $identityRepository,
        LoginForm $loginForm,
        RequestInterface $request,
        ResponseFactoryInterface $responseFactory,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): ResponseInterface {
        $body = $request->getParsedBody();
        $error = null;

        if (
            $request->getMethod() === Method::POST
            && $loginForm->load($body)
            && $validator->validate($loginForm)->isValid()
        ) {
            /** @var User $identity */
            $identity = $identityRepository->findByLogin($loginForm->getAttributeValue('login'));

            if ($identity !== null) {
                $loginForm->getFormErrors()->addError('password', 'Unable to register user with such username.');
            } else {
                $user = new User($loginForm->getAttributeValue('login'), $loginForm->getAttributeValue('password'));
                $entityWriter->write([$user]);

                return $responseFactory
                    ->createResponse(302)
                    ->withHeader(
                        'Location',
                        $urlGenerator->generate('site/index')
                    );
            }
        }

        return $this->viewRenderer->render(
            'signup',
            [
                'body' => $body,
                'formModel' => $loginForm,
                'error' => $error,
            ]
        );
    }
}
