<?php

namespace Lionix\CastableRequest\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Lionix\CastableRequest\CastsRegistry;

class CastsRegistryTest extends TestCase
{
    use WithFaker;

    /**
     * @var \Lionix\CastableRequest\CastsRegistry
     */
    private $registry;

    public function setUp(): void
    {
        parent::setUp();
        $this->registry = $this->app->make(CastsRegistry::class);
    }

    public function testRegisterAndGetAll()
    {
        $this->registry->register($key = $this->faker->slug, $cast = $this->faker->slug);

        $this->assertArrayHasKey($key, $this->registry->all());
        $this->assertSame($cast, $this->registry->all()[$key]);
    }
}
