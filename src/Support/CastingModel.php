<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * Throwaway model used to reuse Eloquent's attribute casting.
 *
 * @internal
 */
final class CastingModel extends Model
{
    private const KEY = 'value';

    /**
     * Casts whose raw value is expected to be a JSON string.
     */
    private const JSON_CASTS = ['array', 'json', 'json:unicode', 'object', 'collection'];

    /**
     * Use a fixed format so date casts never need a database connection.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    public function castValue(mixed $value, string $cast): mixed
    {
        $this->casts = [self::KEY => $cast];

        // Request input is already decoded, so re-encode structured values
        // before handing them to Eloquent's JSON based casts.
        if (! is_string($value) && $value !== null && in_array($this->getCastType(self::KEY), self::JSON_CASTS, true)) {
            $value = $this->asJson($value);
        }

        return $this->castAttribute(self::KEY, $value);
    }
}
