<?php

declare(strict_types=1);

namespace App\Auth;

use OpenApi\Annotations as OA;
use Yiisoft\Input\Http\Attribute\Parameter\Body;
use Yiisoft\Input\Http\RequestInputInterface;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

/**
 * @OA\Schema(
 *      schema="AuthRequest",
 *
 *      @OA\Property(example="Opal1144", property="login", format="string"),
 *      @OA\Property(example="Opal1144", property="password", format="string"),
 * )
 */
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
