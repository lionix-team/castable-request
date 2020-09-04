<?php

namespace Lionix\CastableRequest;

use Illuminate\Foundation\Http\FormRequest;
use Lionix\CastableRequest\Contracts\CastableRequestInterface;
use Lionix\CastableRequest\Contracts\CastsRegistryInterface;
use Lionix\CastableRequest\Contracts\RequestInputCasterInterface;

class FormRequestAfterResolvingHandler
{
    /**
     * @var \Lionix\CastableRequest\Contracts\CastsRegistryInterface
     */
    private $registry;

    /**
     * @var \Lionix\CastableRequest\Contracts\RequestInputCasterInterface
     */
    private $caster;

    /**
     * @param \Lionix\CastableRequest\Contracts\CastsRegistryInterface $registry
     * @param \Lionix\CastableRequest\Contracts\RequestInputCasterInterface $caster
     */
    public function __construct(
        CastsRegistryInterface $registry,
        RequestInputCasterInterface $caster
    ) {
        $this->registry = $registry;
        $this->caster = $caster;
    }

    /**
     * Register request casts and apply registry castings to the request.
     *
     * @param \Illuminate\Foundation\Http\FormRequest $request
     *
     * @return void
     */
    public function handle(FormRequest $request): void
    {
        if ($request instanceof CastableRequestInterface) {
            foreach ($request->casts() as $attribute => $cast) {
                $this->registry->register($attribute, $cast);
            }
        }

        foreach ($this->registry->all() as $attribute => $cast) {
            $this->caster->castAttribute($request, $attribute, $cast);
        }
    }
}
