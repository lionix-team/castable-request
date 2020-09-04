<?php

namespace Lionix\CastableRequest;

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
     * Bind form request after resolving handler.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->afterResolving(FormRequest::class, function (FormRequest $request, Application $app) {
            $app->call(FormRequestAfterResolvingHandler::class . '@handle', compact('request'));
        });
    }
}
