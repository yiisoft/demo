<?php


namespace App\Controller;

use App\Entity\User;
use App\ViewRenderer;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;

final class SignupController
{
    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('signup');
    }

    public function signup(
        RequestInterface $request,
        IdentityRepositoryInterface $identityRepository,
        ORMInterface $orm,
        UrlGeneratorInterface $urlGenerator,
        LoggerInterface $logger,
        ResponseFactoryInterface $responseFactory
    ): ResponseInterface {
        $body = $request->getParsedBody();
        $error = null;

        if ($request->getMethod() === Method::POST) {
            try {
                foreach (['login', 'password'] as $name) {
                    if (empty($body[$name])) {
                        throw new \InvalidArgumentException(ucfirst($name) . ' is required.');
                    }
                }

                /** @var \App\Entity\User $identity */
                $identity = $identityRepository->findByLogin($body['login']);
                if ($identity !== null) {
                    throw new \InvalidArgumentException('Unable to register user with such username.');
                }

                $user = new User($body['login'], $body['password']);

                $transaction = new Transaction($orm);
                $transaction->persist($user);

                $transaction->run();
                return $responseFactory
                    ->createResponse(302)
                    ->withHeader(
                        'Location',
                        $urlGenerator->generate('site/index')
                    );
            } catch (\Throwable $e) {
                $logger->error($e);
                $error = $e->getMessage();
            }
        }

        return $this->viewRenderer->render(
            'signup',
            [
                'body' => $body,
                'error' => $error,
                'csrf' => $request->getAttribute('csrf_token'),
            ]
        );
    }
}
