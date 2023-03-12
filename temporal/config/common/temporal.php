<?php
declare(strict_types=1);

use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowClientInterface;

return [
    WorkflowClientInterface::class => WorkflowClient::create(ServiceClient::create('localhost:7233')),
];
