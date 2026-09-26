<?php

declare(strict_types=1);

namespace Lionix\CastableRequest;

use Lionix\CastableRequest\Contracts\CasterInterface;
use Lionix\CastableRequest\Support\CastingModel;

class EloquentModelCaster implements CasterInterface
{
    /**
     * Cast the given value using Eloquent attribute casting.
     *
     * A fresh model is used for every value so Eloquent's internal cast
     * caches never leak between attributes.
     */
    public function cast(mixed $value, string $cast): mixed
    {
        return (new CastingModel)->castValue($value, $cast);
    }
}
