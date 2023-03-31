<?php

declare(strict_types=1);

namespace App\User;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *      schema="User",
 *
 *      @OA\Property(example="UserName", property="login", format="string"),
 *      @OA\Property(example="13.12.2020 00:04:20", property="created_at", format="string"),
 * )
 */
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
