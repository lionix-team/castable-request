<?php

namespace Lionix\CastableRequest;

use Lionix\CastableRequest\Contracts\CastsRegistryInterface;

class ImMemoryCastsRegistry implements CastsRegistryInterface
{
    /**
     * @var array
     */
    private $casts = [
        //
    ];

    /**
     * @inheritDoc
     */
    public function register(string $attribute, string $cast)
    {
        $this->casts[$attribute] = $cast;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->casts;
    }
}
