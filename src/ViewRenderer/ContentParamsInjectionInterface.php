<?php

declare(strict_types=1);

namespace App\ViewRenderer;

interface ContentParamsInjectionInterface
{

    /**
     * Returns parameters for added to content.
     *
     * For example:
     *
     * ```
     * [
     *     'paramA' => 'something',
     *     'paramB' => 42,
     *     ...
     * ]
     * ```
     *
     * @return array
     */
    public function getContentParams(): array;
}
