<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Contracts;

interface CasterInterface
{
    /**
     * Cast the given value using the given cast definition.
     */
    public function cast(mixed $value, string $cast): mixed;
}
