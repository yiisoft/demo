<?php
declare(strict_types=1);

namespace App\Temporal\Workflow;

use App\Temporal\Activity\CommonActivity;
use Temporal\Activity\ActivityOptions;
use Temporal\Promise;
use Temporal\Workflow;

#[\Temporal\Workflow\WorkflowInterface]
final class FastWorkflow
{
    #[\Temporal\Workflow\WorkflowMethod("fast_workflow")]
    public function run(string $name, int $count): \Generator
    {
        $activity = Workflow::newActivityStub(
            CommonActivity::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(5)
        );
        $promises = [];

        foreach (range(1, $count) as $item) {
            $promises[] = $activity->fast($name);
        }

        return yield Promise::all($promises);
    }
}