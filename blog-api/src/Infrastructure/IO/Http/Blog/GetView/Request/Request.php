<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\GetView\Request;

use Yiisoft\Hydrator\Temp\RouteArgument;
use Yiisoft\Input\Http\AbstractInput;

final class Request extends AbstractInput
{
    #[RouteArgument('id')]
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }
}
