<?php

namespace Lionix\CastableRequest;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Lionix\CastableRequest\Contracts\CasterInterface;
use Lionix\CastableRequest\Contracts\CastsRegistryInterface;
use Lionix\CastableRequest\Contracts\RequestInputCasterInterface;
use Lionix\CastableRequest\Handlers\RequestAfterResolvingHandler;
use Lionix\CastableRequest\Handlers\RequestResolvingHandler;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        RequestInputCasterInterface::class => RequestInputCaster::class,
        CasterInterface::class => EloquentModelCaster::class,
    ];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        CastsRegistryInterface::class => ImMemoryCastsRegistry::class,
    ];

    /**
     * Bind form request resolving handlers.
     *
     * @return void
     */
    public function register()
    {
        $this->app->resolving(FormRequest::class, $this->handler(RequestResolvingHandler::class));
        $this->app->afterResolving(FormRequest::class, $this->handler(RequestAfterResolvingHandler::class));
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
        return function (Request $request, Application $app) use ($handler) {
            $app->call($handler . '@handle', compact('request'));
        };
    }
}
