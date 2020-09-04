<?php

namespace Lionix\CastableRequest\Contracts;

use Illuminate\Http\Request;

interface RequestInputCasterInterface
{
    /**
     * Cast request input attribute to a given cast.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $attribute
     * @param string $cast
     *
     * @return void
     */
    public function castAttribute(Request $request, string $attribute, string $cast);
}
