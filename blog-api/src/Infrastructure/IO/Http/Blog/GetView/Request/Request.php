<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Blog\GetView\Request;

use Yiisoft\RequestModel\RequestModel;

final class Request extends RequestModel
{
    public function getId(): int
    {
        return (int)$this->getAttributeValue('router.id');
    }
}
