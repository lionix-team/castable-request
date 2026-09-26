<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Tests\Unit;

use Illuminate\Http\Request;
use Lionix\CastableRequest\Contracts\CasterInterface;
use Lionix\CastableRequest\RequestInputCaster;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class RequestInputCasterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private CasterInterface&MockInterface $caster;

    private RequestInputCaster $requestCaster;

    protected function setUp(): void
    {
        parent::setUp();

        $this->caster = Mockery::mock(CasterInterface::class);
        $this->requestCaster = new RequestInputCaster($this->caster);
    }

    public function test_it_skips_missing_attributes(): void
    {
        $request = new Request(['present' => 'value']);

        $this->caster->shouldNotReceive('cast');

        $this->requestCaster->castAttribute($request, 'missing', 'bool');
        $this->requestCaster->castAttribute($request, 'missing.*', 'bool');
        $this->requestCaster->castAttribute($request, 'present.*', 'bool');

        $this->assertSame(['present' => 'value'], $request->input());
    }

    public function test_it_casts_an_existing_attribute(): void
    {
        $request = new Request(['one' => 'yes', 'two' => 'untouched']);

        $this->caster->shouldReceive('cast')->once()->with('yes', 'bool')->andReturn(true);

        $this->requestCaster->castAttribute($request, 'one', 'bool');

        $this->assertSame(['one' => true, 'two' => 'untouched'], $request->input());
    }

    public function test_it_casts_null_values_of_present_attributes(): void
    {
        $request = new Request(['one' => null]);

        $this->caster->shouldReceive('cast')->once()->with(null, 'bool')->andReturn(false);

        $this->requestCaster->castAttribute($request, 'one', 'bool');

        $this->assertFalse($request->input('one'));
    }

    public function test_it_casts_nested_and_wildcard_attributes(): void
    {
        $request = new Request([
            'nested' => [
                'list' => ['a', 'b'],
                'single' => 'c',
                'people' => [
                    ['name' => 'd'],
                    ['name' => 'e'],
                    ['other' => 'f'],
                ],
            ],
        ]);

        $this->caster->shouldReceive('cast')->times(5)->andReturnUsing(
            fn (string $value, string $cast): string => strtoupper($value).":{$cast}"
        );

        $this->requestCaster->castAttributes($request, [
            'nested.list.*' => 'x',
            'nested.single' => 'y',
            'nested.people.*.name' => 'z',
        ]);

        $this->assertSame([
            'nested' => [
                'list' => ['A:x', 'B:x'],
                'single' => 'C:y',
                'people' => [
                    ['name' => 'D:z'],
                    ['name' => 'E:z'],
                    ['other' => 'f'],
                ],
            ],
        ], $request->input());
    }

    public function test_it_supports_top_level_wildcards(): void
    {
        $request = new Request([['id' => '1'], ['id' => '2']]);

        $this->caster->shouldReceive('cast')->twice()->andReturnUsing(fn (string $value): int => (int) $value);

        $this->requestCaster->castAttribute($request, '*.id', 'int');

        $this->assertSame([['id' => 1], ['id' => 2]], $request->input());
    }
}
