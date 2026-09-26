<?php

declare(strict_types=1);

namespace Lionix\CastableRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Lionix\CastableRequest\Contracts\CasterInterface;
use Lionix\CastableRequest\Contracts\RequestInputCasterInterface;

class RequestInputCaster implements RequestInputCasterInterface
{
    public function __construct(
        private readonly CasterInterface $caster,
    ) {}

    public function castAttribute(Request $request, string $attribute, string $cast): void
    {
        $this->castAttributes($request, [$attribute => $cast]);
    }

    public function castAttributes(Request $request, array $casts): void
    {
        if ($casts === []) {
            return;
        }

        $input = $request->input();
        $changed = false;

        foreach ($casts as $attribute => $cast) {
            foreach ($this->resolvePaths($input, $attribute) as $path) {
                Arr::set($input, $path, $this->caster->cast(Arr::get($input, $path), $cast));
                $changed = true;
            }
        }

        if ($changed) {
            $this->replaceInput($request, $input);
        }
    }

    /**
     * Replace the request input source with the casted input.
     *
     * Symfony's InputBag rejects objects such as enums, so the parameters are
     * written directly instead of going through Request::replace().
     *
     * @param  array<array-key, mixed>  $input
     */
    protected function replaceInput(Request $request, array $input): void
    {
        $source = match (true) {
            $request->isJson() => $request->json(),
            in_array($request->getRealMethod(), ['GET', 'HEAD'], true) => $request->query,
            default => $request->request,
        };

        (fn () => $this->parameters = $input)->call($source);
    }

    /**
     * Expand the given attribute into the concrete input paths it matches.
     *
     * @param  array<array-key, mixed>  $input
     * @return list<string>
     */
    protected function resolvePaths(array $input, string $attribute): array
    {
        if (! str_contains($attribute, '*')) {
            return Arr::has($input, $attribute) ? [$attribute] : [];
        }

        [$prefix, $suffix] = explode('*', $attribute, 2);
        $prefix = rtrim($prefix, '.');

        $items = $prefix === '' ? $input : Arr::get($input, $prefix);

        if (! is_array($items)) {
            return [];
        }

        $paths = [];

        foreach (array_keys($items) as $key) {
            $path = ($prefix === '' ? '' : $prefix.'.').$key.$suffix;

            array_push($paths, ...$this->resolvePaths($input, $path));
        }

        return $paths;
    }
}
