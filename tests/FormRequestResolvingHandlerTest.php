<?php

namespace Lionix\CastableRequest\Tests;

use Illuminate\Foundation\Http\FormRequest;
use Lionix\CastableRequest\Contracts\CastsRegistryInterface;
use Lionix\CastableRequest\Contracts\RequestInputCasterInterface;
use Lionix\CastableRequest\FormRequestAfterResolvingHandler;
use Lionix\CastableRequest\FormRequestResolvingHandler;
use Mockery;
use Mockery\MockInterface;

class FormRequestResolvingHandlerTest extends TestCase
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
     * @var \Lionix\CastableRequest\FormRequestAfterResolvingHandler
     */
    private $handler;

    public function setUp(): void
    {
        parent::setUp();
        $this->registry = Mockery::mock(CastsRegistryInterface::class);
        $this->caster = Mockery::mock(RequestInputCasterInterface::class);
        $this->app->instance(RequestInputCasterInterface::class, $this->caster);
        $this->app->instance(CastsRegistryInterface::class, $this->registry);
        $this->handler = $this->app->make(FormRequestResolvingHandler::class);
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
