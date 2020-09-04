<?php

namespace Lionix\CastableRequest\Tests;

use Lionix\CastableRequest\FormRequestAfterResolvingHandler;
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
        $this->handler = Mockery::mock(FormRequestAfterResolvingHandler::class);
        $this->app->instance(FormRequestAfterResolvingHandler::class, $this->handler);
    }

    public function testBindFormRequestAfterResolvingHandler()
    {
        $this->handler->shouldReceive('handle')->with(Mockery::type(CastableRequest::class));
        $this->app->make(CastableRequest::class);
    }
}
