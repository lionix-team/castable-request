<?php

namespace Lionix\CastableRequest\Handlers;

use Illuminate\Http\Request;
use Lionix\CastableRequest\Contracts\CastableRequestInterface;
use Lionix\CastableRequest\Contracts\CastsRegistryInterface;

class RequestResolvingHandler
{
    /**
     * @var \Lionix\CastableRequest\Contracts\CastsRegistryInterface
     */
    private $registry;

    /**
     * @param \Lionix\CastableRequest\Contracts\CastsRegistryInterface $registry
     */
    public function __construct(CastsRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Register request casts.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function handle(Request $request): void
    {
        if ($request instanceof CastableRequestInterface) {
            foreach ($request->casts() as $attribute => $cast) {
                $this->registry->register($attribute, $cast);
            }
        }
    }
}
