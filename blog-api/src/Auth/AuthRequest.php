<?php

declare(strict_types=1);

namespace App\Auth;

use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\RequestInputInterface;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AuthRequest',
    properties: [
        new OA\Property(property: 'login', type: 'string', example: 'Opal1144'),
        new OA\Property(property: 'password', type: 'string', example: 'Opal1144'),
    ]
)]
final class AuthRequest implements RequestInputInterface, RulesProviderInterface
{
    #[Body('login')]
    private string $login = '';

    #[Body('password')]
    private string $password = '';

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRules(): array
    {
        return [
            'login' => [
                new Required(),
            ],
            'password' => [
                new Required(),
            ],
        ];
    }
}
