<?php

declare(strict_types=1);

namespace Lionix\CastableRequest;

use Lionix\CastableRequest\Contracts\CastsRegistryInterface;

class InMemoryCastsRegistry implements CastsRegistryInterface
{
    /**
     * @var array<string, string>
     */
    private array $casts = [];

    public function register(string $attribute, string $cast): void
    {
        $this->casts[$attribute] = $cast;
    }

    public function forget(string $attribute): void
    {
        unset($this->casts[$attribute]);
    }

    public function all(): array
    {
        return $this->casts;
    }
}
