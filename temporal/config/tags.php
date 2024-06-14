<?php

declare(strict_types=1);

use Temporal\Activity\ActivityInterface;
use Temporal\Workflow\WorkflowInterface;
use Yiisoft\Classifier\Classifier;

$classifier = new Classifier(dirname(__DIR__) . '/src');

return [
    'temporal.workflow' => [
        ...iterator_to_array($classifier->withAttribute(WorkflowInterface::class)->find()),
    ],
    'temporal.activity' => [
        ...iterator_to_array($classifier->withAttribute(ActivityInterface::class)->find()),
    ],
];
