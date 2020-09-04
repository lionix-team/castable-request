<?php

namespace Lionix\CastableRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Lionix\CastableRequest\Contracts\CasterInterface;
use Lionix\CastableRequest\Contracts\RequestInputCasterInterface;

class RequestInputCaster implements RequestInputCasterInterface
{
    /**
     * @var \Lionix\CastableRequest\Contracts\CasterInterface
     */
    private $caster;

    /**
     * @param \Lionix\CastableRequest\Contracts\CasterInterface $caster
     */
    public function __construct(CasterInterface $caster)
    {
        $this->caster = $caster;
    }

    /**
     * @inheritDoc
     */
    public function castAttribute(Request $request, string $attribute, string $cast)
    {
        $input = $request->input();

        foreach ($this->resolveRequestAttributes($request, $attribute) as $attr) {
            Arr::set($input, $attr, $this->caster->cast(Arr::get($input, $attr), $cast));
        }

        $request->replace($input);
    }

    /**
     * Get all matched request input attributes recursively.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $attribute
     *
     * @return array
     */
    protected function resolveRequestAttributes(
        Request $request,
        string $attribute
    ): array{
        if (!str_contains($attribute, '.*')) {
            return $request->has($attribute) ? Arr::wrap($attribute) : [];
        }

        $prefix = Str::before($attribute, '.*');
        $postfix = Str::after($attribute, '.*');

        $input = $request->input($prefix);

        return !is_array($input) ? [] : collect($input)->keys()->reduce(
            function ($aggregate, $current) use ($request, $prefix, $postfix) {
                return array_merge(
                    $aggregate,
                    $this->resolveRequestAttributes(
                        $request,
                        $prefix . '.' . $current . $postfix
                    )
                );
            },
            []
        );
    }
}
