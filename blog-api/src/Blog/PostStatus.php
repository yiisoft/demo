<?php

declare(strict_types=1);

namespace App\Blog;

use MyCLabs\Enum\Enum;

/**
 * @method static PostStatus PUBLIC()
 * @method static PostStatus DRAFT()
 * @method static PostStatus DELETED()
 *
 * @psalm-immutable
 */
final class PostStatus extends Enum
{
    private const PUBLIC = 0;
    private const DRAFT = 1;
    private const DELETED = 2;
}
