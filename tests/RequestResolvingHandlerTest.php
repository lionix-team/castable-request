<?php

namespace Lionix\CastableRequest\Tests;

use Lionix\CastableRequest\Contracts\CastsRegistryInterface;
use Lionix\CastableRequest\Contracts\RequestInputCasterInterface;
use Lionix\CastableRequest\Handlers\RequestResolvingHandler;
use Mockery;

class RequestResolvingHandlerTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface
     */
    private $caster;

    /**
     * @var \Mockery\MockInterface
     */
    private $registry;

    /**
     * @var \Lionix\CastableRequest\Handlers\RequestAfterResolvingHandler
     */
    private $handler;

    public function setUp(): void
    {
        parent::setUp();
        $this->registry = Mockery::mock(CastsRegistryInterface::class);
        $this->caster = Mockery::mock(RequestInputCasterInterface::class);
        $this->app->instance(RequestInputCasterInterface::class, $this->caster);
        $this->app->instance(CastsRegistryInterface::class, $this->registry);
        $this->handler = $this->app->make(RequestResolvingHandler::class);
    }

    public function testAddRequestCastsToTheRegistry()
    {
        $casts = [
            'one' => 'string',
            'two' => 'bool',
            'three' => 'array',
        ];

        foreach ($casts as $attribute => $cast) {
            $this->registry->shouldReceive('register')->once()->withArgs([$attribute, $cast]);
        }

        $request = Mockery::mock(CastableRequest::class);
        $request->shouldReceive('casts')->once()->andReturn($casts);

        $this->handler->handle($request);
    }
}
