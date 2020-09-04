<?php

namespace Lionix\CastableRequest\Contracts;

interface CastsRegistryInterface
{
    /**
     * Register casts to be executed.
     *
     * @param string $attribute
     * @param string $casts
     *
     * @return void
     */
    public function register(string $attribute, string $cast);

    /**
     * Get all registered casts.
     *
     * @return array
     */
    public function all(): array;
}
