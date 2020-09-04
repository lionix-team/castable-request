<?php

namespace Lionix\CastableRequest\Tests;

use Illuminate\Foundation\Http\FormRequest;
use Lionix\CastableRequest\Contracts\CastableRequestInterface;

class CastableRequest extends FormRequest implements CastableRequestInterface
{
    /**
     * @inheritDoc
     */
    public function casts(): array
    {
        return [
            //
        ];
    }

    /**
     * Request validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
