<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Contracts;

interface CastsRegistryInterface
{
    /**
     * Register a global cast for the given input attribute.
     */
    public function register(string $attribute, string $cast): void;

    /**
     * Remove the global cast registered for the given input attribute.
     */
    public function forget(string $attribute): void;

    /**
     * Get all registered global casts.
     *
     * @return array<string, string>
     */
    public function all(): array;
}
