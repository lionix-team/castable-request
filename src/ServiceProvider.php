<?php

namespace Lionix\CastableRequest;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Lionix\CastableRequest\Contracts\CasterInterface;
use Lionix\CastableRequest\Contracts\CastsRegistryInterface;
use Lionix\CastableRequest\Contracts\RequestInputCasterInterface;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        RequestInputCasterInterface::class => RequestInputCaster::class,
        CasterInterface::class => ModelCaster::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        CastsRegistryInterface::class => CastsRegistry::class,
    ];

    /**
     * Bind form request resolving handlers.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->resolving(FormRequest::class, $this->handler(FormRequestResolvingHandler::class));
        $this->app->afterResolving(FormRequest::class, $this->handler(FormRequestAfterResolvingHandler::class));
    }

    /**
     * Request resolving bindings shorthand.
     *
     * @param string $handler
     *
     * @return \Closure
     */
    private function handler(string $handler): Closure
    {
        return function (FormRequest $request, Application $app) use ($handler) {
            $app->call($handler . '@handle', compact('request'));
        };
    }
}
