<?php

namespace Lionix\CastableRequest\Tests;

use Lionix\CastableRequest\Contracts\CastsRegistryInterface;
use Lionix\CastableRequest\Contracts\RequestInputCasterInterface;
use Lionix\CastableRequest\Handlers\RequestAfterResolvingHandler;
use Mockery;
use Mockery\MockInterface;

class RequestAfterResolvingHandlerTest extends TestCase
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
        $this->handler = $this->app->make(RequestAfterResolvingHandler::class);
    }

    public function testCastAttributesUsingTheRegistry()
    {
        $casts = [
            'one' => 'string',
            'two' => 'bool',
            'three' => 'array',
        ];

        $this->registry->shouldReceive('all')->once()->andReturn($casts);

        $request = Mockery::mock(CastableRequest::class)->makePartial();

        foreach ($casts as $attribute => $cast) {
            $this->caster->shouldReceive('castAttribute')->once()->withArgs([$request, $attribute, $cast]);
        }

        $this->handler->handle($request);
    }
}
