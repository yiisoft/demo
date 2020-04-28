<?php

namespace App\Contact;

use App\Controller;
use App\Parameters;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Http\Method;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Web\User\User;
use Yiisoft\Yii\Web\Data\DataResponseFactoryInterface;

class ContactController extends Controller
{
    private ContactMailer $mailer;
    private LoggerInterface $logger;
    private Parameters $parameters;

    public function __construct(
        DataResponseFactoryInterface $responseFactory,
        Aliases $aliases,
        WebView $view,
        User $user,
        ContactMailer $mailer,
        LoggerInterface $logger,
        Parameters $parameters
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
        parent::__construct($responseFactory, $user, $aliases, $view);
        $this->parameters = $parameters;
    }

    protected function getId(): string
    {
        return 'contact';
    }

    public function contact(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        $parameters = [
            'body' => $body,
        ];
        if ($request->getMethod() === Method::POST) {
            $sent = false;
            $error = '';

            try {
                foreach (['subject', 'name', 'email', 'content'] as $name) {
                    if (empty($body[$name])) {
                        throw new \InvalidArgumentException(ucfirst($name) . ' is required');
                    }
                }

                $message = new Message($body['name'], $body['email'], $body['subject'], $body['content']);

                $files = $request->getUploadedFiles();
                if (!empty($files['file']) && $files['file']->getError() === UPLOAD_ERR_OK) {
                    $message->addFile($files['file']);
                }

                $this->mailer->send($message);

                $sent = true;
            } catch (\Throwable $e) {
                throw $e;
                $this->logger->error($e);
                $error = $e->getMessage();
            }
            $parameters['sent'] = $sent;
            $parameters['error'] = $error;
        }

        $parameters['csrf'] = $request->getAttribute('csrf_token');

        return $this->render('form', $parameters);
    }
}
