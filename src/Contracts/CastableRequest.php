<?php

namespace Lionix\CastableRequest\Contracts;

interface CastableRequest
{
    /**
     * Get request casts.
     *
     * @return array
     */
    public function casts(): array;
}
