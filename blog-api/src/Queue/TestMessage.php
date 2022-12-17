<?php

namespace App\Queue;

use Yiisoft\Yii\Queue\Message\MessageInterface;

class TestMessage implements MessageInterface
{
    private ?string $id = null;

    public function __construct(private string $some_id, private int $time)
    {
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getHandlerName(): string
    {
        return TestHandler::NAME;
    }

    public function getData(): array
    {
        return [
            'some_id' => $this->some_id,
            'time' => $this->time,
        ];
    }

    public function getMetadata(): array
    {
        return [];
    }
}
