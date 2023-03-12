<?php
declare(strict_types=1);

namespace App\Temporal\Workflow;

use App\Temporal\Activity\CommonActivity;
use Temporal\Activity\ActivityOptions;
use Temporal\Promise;
use Temporal\Workflow;

#[\Temporal\Workflow\WorkflowInterface]
final class LongWorkflow
{
    private array $done = [];
    private string $status = 'start';
    private float $start = 999999999999;
    private float $end = 0;

    #[Workflow\QueryMethod]
    public function getStatus(): array
    {
        $time = $this->end > 0 ? $this->end - $this->start : '---';
        $status = $this->status === 'done'
            ? sprintf(
                'Processed %d tasks in %f seconds.',
                count($this->done),
                $time
            )
            : $this->status;

        return [
            'status' => $status,
            'total time' => $time,
            'done' => $this->done,
        ];
    }

    #[\Temporal\Workflow\WorkflowMethod("long_workflow")]
    public function run(string $name, int $count): \Generator
    {
        $activity = Workflow::newActivityStub(
            CommonActivity::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(6)
        );

        $promises = [];

        foreach (range(1, $count) as $item) {
            $promises[] = $activity->slow($name)
                ->then(
                    function ($result) use ($item) {
                        $this->start = min($this->start, $result['start']);
                        $this->end = max($this->end, $result['end']);
                        $this->done['Task #' . $item] = $result;
                        return $result;
                    }
                );
        }

        $this->status = 'processing';

        $result = yield Promise::all($promises);

        $this->status = 'done';

        return $result;
    }
}
