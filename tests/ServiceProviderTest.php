<?php

namespace Lionix\CastableRequest\Tests;

use Lionix\CastableRequest\Handlers\RequestAfterResolvingHandler;
use Lionix\CastableRequest\Handlers\RequestResolvingHandler;
use Mockery;

class ServiceProviderTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected function getPackageProviders($app)
    {
        return [
            \Lionix\CastableRequest\ServiceProvider::class,
        ];
    }

    /**
     * @var \Mockery\MockInterface
     */
    private $handler;

    public function setUp(): void
    {
        parent::setUp();
        $this->resolvingHandler = Mockery::mock(RequestResolvingHandler::class);
        $this->afterResolvingHandler = Mockery::mock(RequestAfterResolvingHandler::class);
        $this->app->instance(RequestResolvingHandler::class, $this->resolvingHandler);
        $this->app->instance(RequestAfterResolvingHandler::class, $this->afterResolvingHandler);
    }

    public function testBindRequestHandlers()
    {
        $this->resolvingHandler->shouldReceive('handle')->with(Mockery::type(CastableRequest::class));
        $this->afterResolvingHandler->shouldReceive('handle')->with(Mockery::type(CastableRequest::class));
        $this->app->make(CastableRequest::class);
    }
}
