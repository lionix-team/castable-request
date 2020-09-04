<?php

namespace Lionix\CastableRequest\Contracts;

interface CasterInterface
{
    /**
     * Cast value with given cast.
     *
     * @param mixed $value
     * @param string $cast
     *
     * @return mixed
     */
    public function cast($value, string $cast);
}
