<?php

namespace Lionix\CastableRequest;

use Illuminate\Foundation\Http\FormRequest;
use Lionix\CastableRequest\Contracts\CastableRequestInterface;
use Lionix\CastableRequest\Contracts\CastsRegistryInterface;

class FormRequestResolvingHandler
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
    }
}
