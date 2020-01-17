<?php
namespace App\Controller;

use App\Controller;
use hiqdev\composer\config\Builder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Http\Method;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Web\User\User;

class ContactController extends Controller
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        Aliases $aliases,
        WebView $view,
        User $user,
        MailerInterface $mailer,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
        parent::__construct($responseFactory, $user, $aliases, $view);
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
            $config = require Builder::path('params');
            $sent = false;
            $error = '';
            try {
                foreach (['subject', 'name', 'email', 'content'] as $name) {
                    if (empty($body[$name])) {
                        throw new \InvalidArgumentException(ucfirst($name) . ' is required');
                    }
                }
                $message = $this->mailer->compose('contact', [
                    'name' => $body['name'],
                    'email' => $body['email'],
                    'content' => $body['content']
                ])
                    ->setSubject($body['subject'])
                    ->setTo($config['supportEmail'])
                    ->setFrom($config['mailer']['username']);

                /** @var UploadedFileInterface[] $files */
                $files = $request->getUploadedFiles();
                if (!empty($files['file']) && $files['file']->getError() === UPLOAD_ERR_OK) {
                    $file = $files['file'];
                    $message->attachContent((string)$file->getStream(), ['fileName' => $file->getClientFilename(), 'contentType' => $file->getClientMediaType()]);
                }

                $message->send();
                $sent = true;
            } catch (\Throwable $e) {
                $this->logger->error($e);
                $error = $e->getMessage();
            }
            $parameters['sent'] = $sent;
            $parameters['error'] = $error;
        }

        $response = $this->responseFactory->createResponse();

        // $this->layout = null;
        $output = $this->render('form', $parameters);

        $response->getBody()->write($output);
        return $response;
    }
}
