<?php

namespace Lionix\CastableRequest\Handlers;

use Illuminate\Http\Request;
use Lionix\CastableRequest\Contracts\CastsRegistryInterface;
use Lionix\CastableRequest\Contracts\RequestInputCasterInterface;

class RequestAfterResolvingHandler
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
     * Apply registry castings to the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function handle(Request $request): void
    {
        foreach ($this->registry->all() as $attribute => $cast) {
            $this->caster->castAttribute($request, $attribute, $cast);
        }
    }
}
