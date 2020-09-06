<?php

namespace Lionix\CastableRequest\Tests;

use Lionix\CastableRequest\FormRequestAfterResolvingHandler;
use Lionix\CastableRequest\FormRequestResolvingHandler;
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
        $this->resolvingHandler = Mockery::mock(FormRequestResolvingHandler::class);
        $this->afterResolvingHandler = Mockery::mock(FormRequestAfterResolvingHandler::class);
        $this->app->instance(FormRequestResolvingHandler::class, $this->resolvingHandler);
        $this->app->instance(FormRequestAfterResolvingHandler::class, $this->afterResolvingHandler);
    }

    public function testBindFormRequestAfterResolvingHandler()
    {
        $this->resolvingHandler->shouldReceive('handle')->with(Mockery::type(CastableRequest::class));
        $this->afterResolvingHandler->shouldReceive('handle')->with(Mockery::type(CastableRequest::class));
        $this->app->make(CastableRequest::class);
    }
}
