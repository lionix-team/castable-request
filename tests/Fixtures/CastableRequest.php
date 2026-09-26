<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Tests\Fixtures;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lionix\CastableRequest\Contracts\CastableRequestInterface;

class CastableRequest extends FormRequest implements CastableRequestInterface
{
    public function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'status' => Status::class,
            'count' => 'integer',
            'tags.*' => WrapCast::class,
            'items.*.price' => 'decimal:2',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'published_at' => ['sometimes', 'date'],
            'status' => ['sometimes', Rule::enum(Status::class)],
            'count' => ['sometimes', 'string'],
        ];
    }
}
