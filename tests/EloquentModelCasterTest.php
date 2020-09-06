<?php

namespace Lionix\CastableRequest\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Lionix\CastableRequest\EloquentModelCaster;
use ReflectionObject;

class EloquentModelCasterTest extends TestCase
{
    use WithFaker;

    /**
     * @var \Lionix\CastableRequest\ModelCaster
     */
    private $caster;

    public function setUp(): void
    {
        parent::setUp();
        $this->caster = $this->app->make(EloquentModelCaster::class);
    }

    public function testCastPrimivesUsingEloquentCastAttributeMethod()
    {
        $reflection = new ReflectionObject($this->caster);
        $casts = $reflection->getProperty('casts');
        $casts->setAccessible(true);
        $reflection->getMethod('castAttribute')->setAccessible(true);

        foreach ([
            'string',
            'bool',
            'int',
            'integer',
        ] as $cast) {
            $value = $this->faker->realText(50);
            $castableResult = $this->caster->cast($value, $cast);

            $casts->setValue($this->caster, ['attr' => $cast]);
            $this->caster->attr = $value;
            $eloquentResult = $this->caster->attr;

            $this->assertSame($castableResult, $eloquentResult);
        }
    }

    public function testCastWithEloquentCastAttributesClassName()
    {
        $value = $this->faker->realText(10);
        $this->assertSame($this->caster->cast($value, Cast::class), [$value]);
    }
}
