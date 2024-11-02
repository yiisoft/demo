<?php

declare(strict_types=1);

namespace App\User;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'login', type: 'string', example: 'UserName'),
        new OA\Property(property: 'created_at', type: 'string', example: '13.12.2020 00:04:20'),
    ]
)]
final class UserFormatter
{
    public function format(User $user): array
    {
        return [
            'login' => $user->getLogin(),
            'created_at' => $user
                ->getCreatedAt()
                ->format('d.m.Y H:i:s'),
        ];
    }
}
