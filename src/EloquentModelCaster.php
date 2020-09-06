<?php

namespace Lionix\CastableRequest;

use Illuminate\Database\Eloquent\Model;
use Lionix\CastableRequest\Contracts\CasterInterface;

class EloquentModelCaster extends Model implements CasterInterface
{
    /**
     * Use eloquent casting to cast the given value.
     *
     * @param mixed $value
     * @param string $cast
     *
     * @return void
     */
    public function cast($value, string $cast)
    {
        $this->casts = ['attr' => $cast];
        return $this->castAttribute('attr', $value);
    }
}
