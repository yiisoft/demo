<?php
namespace App\Controller;

use App\Controller;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Router\Method;
use Yiisoft\View\WebView;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Yii\Web\User\User;

class AuthController extends Controller
{
    private $logger;

    public function __construct(ResponseFactoryInterface $responseFactory, Aliases $aliases, WebView $view, User $user, LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct($responseFactory, $aliases, $view, $user);
    }

    protected function getId(): string
    {
        return 'auth';
    }

    public function login(ServerRequestInterface $request, IdentityRepositoryInterface $identityRepository): ResponseInterface
    {
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
                    return $this->responseFactory->createResponse(302)->withHeader('Location', '/');
                }

                throw new \InvalidArgumentException('Unable to login');
            } catch (\Throwable $e) {
                $this->logger->error($e);
                $error = $e->getMessage();
            }
        }

        $response = $this->responseFactory->createResponse();

        $output = $this->render('login', [
            'body' => $body,
            'error' => $error,
        ]);

        $response->getBody()->write($output);
        return $response;
    }

    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        $this->user->logout();
        return $this->responseFactory->createResponse(302)->withHeader('Location', '/');
    }
}
