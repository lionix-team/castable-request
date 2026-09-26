<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Tests\Fixtures;

use Illuminate\Foundation\Http\FormRequest;

class PlainRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
