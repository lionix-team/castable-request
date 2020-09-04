<?php

namespace Lionix\CastableRequest\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Lionix\CastableRequest\Contracts\CasterInterface;
use Lionix\CastableRequest\RequestInputCaster;
use Mockery;

class RequestInputCasterTest extends TestCase
{
    use WithFaker;

    /**
     * @var \Mockery\MockInterface
     */
    private $caster;

    /**
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * @var \Lionix\CastableRequest\RequestInputCaster
     */
    private $requestCaster;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->app->make(Request::class);
        $this->caster = Mockery::mock(CasterInterface::class);
        $this->instance(CasterInterface::class, $this->caster);
        $this->requestCaster = $this->app->make(RequestInputCaster::class);
    }

    public function testDoNotPerformCastsIfRequestAttributeIsNotPresent()
    {
        $this->caster->shouldNotReceive('cast');
        $this->requestCaster->castAttribute($this->request, $this->faker->slug, 'bool');
        $this->assertTrue(true);

    }

    public function testCastExistedAttributeWithCaster()
    {
        $input = [
            'one' => $this->faker->realText(10),
            'two' => $this->faker->slug,
            'three' => $this->faker->sha1,
        ];

        $this->request->merge($input);
        $this->caster->shouldReceive('cast')->once()->withArgs([$input['one'], 'bool'])->andReturnNull();
        $this->requestCaster->castAttribute($this->request, 'one', 'bool');
        $this->assertNull($this->request->input('one'));
    }

    public function testCastNestedAttributes()
    {
        $input = [
            'nested' => [
                'two' => [
                    $this->faker->unique()->slug,
                    $this->faker->unique()->slug,
                ],
                'three' => $this->faker->sha1,
                'four' => [
                    ['first_name' => $this->faker->unique()->firstName],
                    ['first_name' => $this->faker->unique()->firstName],
                    ['first_name' => $this->faker->unique()->firstName],
                ],
            ],
        ];

        foreach ($input['nested']['two'] as $value) {
            $this->caster->shouldReceive('cast')->once()->withArgs([$value, 'bool'])->andReturnNull();
        }

        foreach ($input['nested']['four'] as $value) {
            $this->caster->shouldReceive('cast')->once()->withArgs([$value['first_name'], 'bool'])->andReturnNull();
        }

        $this->caster->shouldReceive('cast')->once()->withArgs([$input['nested']['three'], 'bool'])->andReturnNull();

        $this->request->merge($input);
        $this->requestCaster->castAttribute($this->request, 'nested.two.*', 'bool');
        $this->requestCaster->castAttribute($this->request, 'nested.three', 'bool');
        $this->requestCaster->castAttribute($this->request, 'nested.four.*.first_name', 'bool');
        $this->assertTrue(true);
    }
}
