<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Contracts;

use Illuminate\Http\Request;

interface RequestInputCasterInterface
{
    /**
     * Cast a single request input attribute.
     */
    public function castAttribute(Request $request, string $attribute, string $cast): void;

    /**
     * Cast the request input attributes.
     *
     * @param  array<string, string>  $casts
     */
    public function castAttributes(Request $request, array $casts): void;
}
