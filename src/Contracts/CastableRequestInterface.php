<?php

namespace Lionix\CastableRequest\Contracts;

interface CastableRequestInterface
{
    /**
     * Get request casts.
     *
     * @return array
     */
    public function casts(): array;
}
