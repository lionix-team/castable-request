<?php

declare(strict_types=1);

namespace Lionix\CastableRequest;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Lionix\CastableRequest\Contracts\CasterInterface;
use Lionix\CastableRequest\Contracts\CastsRegistryInterface;
use Lionix\CastableRequest\Contracts\RequestInputCasterInterface;
use Lionix\CastableRequest\Handlers\RequestAfterResolvingHandler;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        RequestInputCasterInterface::class => RequestInputCaster::class,
        CasterInterface::class => EloquentModelCaster::class,
    ];

    /**
     * @var array<class-string, class-string>
     */
    public array $singletons = [
        CastsRegistryInterface::class => InMemoryCastsRegistry::class,
    ];

    public function boot(): void
    {
        // Registered while booting so it runs after Laravel's own form request
        // validation callback, i.e. input is validated first and casted after.
        $this->app->afterResolving(
            FormRequest::class,
            static function (FormRequest $request, Application $app): void {
                $app->make(RequestAfterResolvingHandler::class)->handle($request);
            },
        );
    }
}
