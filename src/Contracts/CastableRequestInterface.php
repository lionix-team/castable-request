<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Contracts;

interface CastableRequestInterface
{
    /**
     * Get the casts that should be applied to the request input.
     *
     * Keys are input attributes in "dot" notation (wildcards are supported,
     * e.g. "items.*.price"), values are any Eloquent cast definition.
     *
     * @return array<string, string>
     */
    public function casts(): array;
}
