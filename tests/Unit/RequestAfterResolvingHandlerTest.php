<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Tests\Unit;

use Illuminate\Http\Request;
use Lionix\CastableRequest\Contracts\RequestInputCasterInterface;
use Lionix\CastableRequest\Handlers\RequestAfterResolvingHandler;
use Lionix\CastableRequest\InMemoryCastsRegistry;
use Lionix\CastableRequest\Tests\Fixtures\CastableRequest;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class RequestAfterResolvingHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private InMemoryCastsRegistry $registry;

    private RequestInputCasterInterface&MockInterface $caster;

    private RequestAfterResolvingHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new InMemoryCastsRegistry;
        $this->caster = Mockery::mock(RequestInputCasterInterface::class);
        $this->handler = new RequestAfterResolvingHandler($this->registry, $this->caster);
    }

    public function test_it_applies_global_casts_to_any_request(): void
    {
        $this->registry->register('active', 'boolean');
        $request = new Request;

        $this->caster->shouldReceive('castAttributes')->once()->with($request, ['active' => 'boolean']);

        $this->handler->handle($request);
    }

    public function test_request_casts_are_merged_with_and_override_global_casts(): void
    {
        $this->registry->register('count', 'string');
        $this->registry->register('active', 'boolean');
        $request = new CastableRequest;

        $this->caster->shouldReceive('castAttributes')->once()->with(
            $request,
            array_replace(['count' => 'string', 'active' => 'boolean'], $request->casts()),
        );

        $this->handler->handle($request);
    }

    public function test_request_casts_are_not_added_to_the_global_registry(): void
    {
        $this->caster->shouldReceive('castAttributes');

        $this->handler->handle(new CastableRequest);

        $this->assertSame([], $this->registry->all());
    }
}
