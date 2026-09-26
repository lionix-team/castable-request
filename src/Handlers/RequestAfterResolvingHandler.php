<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Handlers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Lionix\CastableRequest\Contracts\CastableRequestInterface;
use Lionix\CastableRequest\Contracts\CastsRegistryInterface;
use Lionix\CastableRequest\Contracts\RequestInputCasterInterface;

class RequestAfterResolvingHandler
{
    public function __construct(
        private readonly CastsRegistryInterface $registry,
        private readonly RequestInputCasterInterface $caster,
    ) {}

    /**
     * Apply the global casts and the request's own casts to the input.
     *
     * Request casts take precedence over global casts for the same attribute.
     */
    public function handle(Request $request): void
    {
        $casts = $this->registry->all();

        if ($request instanceof CastableRequestInterface) {
            $casts = array_replace($casts, $request->casts());
        }

        if ($casts === []) {
            return;
        }

        $this->caster->castAttributes($request, $casts);

        if ($request instanceof FormRequest) {
            $this->syncValidatorData($request);
        }
    }

    /**
     * Point the validator at the casted input so validated() and safe()
     * return casted values, without validating the casted input again.
     */
    protected function syncValidatorData(FormRequest $request): void
    {
        $validator = (fn (): mixed => $this->validator)->call($request);

        if (! $validator instanceof Validator) {
            return;
        }

        $data = $request->validationData();

        (fn () => $this->data = $this->parseData($data))->call($validator);
    }
}
