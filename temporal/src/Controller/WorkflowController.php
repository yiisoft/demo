<?php
declare(strict_types=1);

namespace App\Controller;

use App\Temporal\Workflow\FastWorkflow;
use App\Temporal\Workflow\LongWorkflow;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Temporal\Client\WorkflowClientInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;

final class WorkflowController
{
    private WorkflowClientInterface $workflowClient;
    private ResponseFactoryInterface $responseFactory;
    private StreamFactoryInterface $streamFactory;

    public function __construct(
        WorkflowClientInterface  $workflowClient,
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface   $streamFactory,
    )
    {
        $this->workflowClient = $workflowClient;
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    public function simpleAction(CurrentRoute $route): ResponseInterface
    {
        $name = (string)$route->getArgument('name');
        $start = microtime(true);

        $wf = $this->workflowClient->newWorkflowStub(FastWorkflow::class);
        $result = $wf->run($name, 5);

        $end = microtime(true);

        $response = [
            'microtime' => $end - $start,
            'result' => $result,
        ];

        return $this->response($response);
    }

    public function complicatedAction(CurrentRoute $route): ResponseInterface
    {
        $name = (string)$route->getArgument('name');
        $start = microtime(true);

        $wf = $this->workflowClient->newWorkflowStub(LongWorkflow::class);
        $result = $wf->run($name, 5);

        $end = microtime(true);

        $response = [
            'microtime' => $end - $start,
            'result' => $result,
        ];

        return $this->response($response);
    }

    public function asynchronousAction(UrlGeneratorInterface $urlGenerator, CurrentRoute $route): ResponseInterface
    {
        $name = (string)$route->getArgument('name');
        $start = microtime(true);

        $wf = $this->workflowClient->newWorkflowStub(LongWorkflow::class);
        $process = $this->workflowClient->start($wf, $name, 10);
        $id = $process->getExecution()->getID();

        $end = microtime(true);

        $url = $urlGenerator->generate('temporal/asynchronous-status', ['id' => $id]);

        $delay = $end - $start;
        $response = <<<HTML
        Microtime: {$delay} <br>
        Job ID: {$id} <br>
        To see status of this job open <a href="{$url}" target="_blank">click here</a>.
        HTML;

        return $this->response($response);
    }

    public function asynchronousStatusAction(CurrentRoute $route)
    {
        $id = (string)$route->getArgument('id');
        $start = microtime(true);

        $wf = $this->workflowClient->newRunningWorkflowStub(LongWorkflow::class, $id);
        $result = $wf->getStatus();

        $end = microtime(true);

        $response = [
            'Note' => 'Please update the page to see results',
            'microtime' => $end - $start,
            'result' => $result,
        ];

        return $this->response($response);
    }

    private function response($data): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(200);
        if (is_string($data)) {
            $content = $data;
            $response = $response->withHeader('Content-Type', 'text/html');
        } else {
            $content = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        }

        return $response->withBody(
            $this->streamFactory->createStream($content)
        );
    }
}
