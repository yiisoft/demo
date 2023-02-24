<?php

declare(strict_types=1);

namespace App\Infrastructure\IO\Http\Auth\PostLogin\Request;

use OpenApi\Annotations as OA;
use Yiisoft\RequestModel\RequestModel;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

/**
 * @OA\Schema(
 *      schema="LoginRequest",
 *      @OA\Property(example="Opal1144", property="login", format="string"),
 *      @OA\Property(example="Opal1144", property="password", format="string"),
 * )
 */
final class Request extends RequestModel implements RulesProviderInterface
{
    public function getLogin(): string
    {
        return (string)$this->getAttributeValue('body.login');
    }

    public function getPassword(): string
    {
        return (string)$this->getAttributeValue('body.password');
    }

    public function getRules(): array
    {
        return [
            'body.login' => [
                new Required(),
            ],
            'body.password' => [
                new Required(),
            ],
        ];
    }
}
