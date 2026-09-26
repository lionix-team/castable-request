<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Tests\Unit;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Lionix\CastableRequest\EloquentModelCaster;
use Lionix\CastableRequest\Tests\Fixtures\Status;
use Lionix\CastableRequest\Tests\Fixtures\WrapCast;
use Lionix\CastableRequest\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class EloquentModelCasterTest extends TestCase
{
    private EloquentModelCaster $caster;

    protected function setUp(): void
    {
        parent::setUp();

        $this->caster = new EloquentModelCaster;
    }

    /**
     * @return iterable<string, array{mixed, string, mixed}>
     */
    public static function primitiveCasts(): iterable
    {
        yield 'int' => ['42', 'int', 42];
        yield 'integer' => ['42', 'integer', 42];
        yield 'float' => ['1.5', 'float', 1.5];
        yield 'bool true' => ['1', 'bool', true];
        yield 'boolean false' => ['0', 'boolean', false];
        yield 'string' => [42, 'string', '42'];
        yield 'decimal' => ['10', 'decimal:2', '10.00'];
        yield 'null stays null' => [null, 'integer', null];
    }

    #[DataProvider('primitiveCasts')]
    public function test_it_casts_primitives(mixed $value, string $cast, mixed $expected): void
    {
        $this->assertSame($expected, $this->caster->cast($value, $cast));
    }

    public function test_it_casts_dates_without_a_database_connection(): void
    {
        $date = $this->caster->cast('2024-05-10', 'date');
        $datetime = $this->caster->cast('2024-05-10 13:45:00', 'datetime');
        $iso = $this->caster->cast('2024-05-10T13:45:00+00:00', 'immutable_datetime');

        $this->assertInstanceOf(Carbon::class, $date);
        $this->assertSame('2024-05-10 00:00:00', $date->toDateTimeString());
        $this->assertInstanceOf(Carbon::class, $datetime);
        $this->assertSame('2024-05-10 13:45:00', $datetime->toDateTimeString());
        $this->assertInstanceOf(CarbonImmutable::class, $iso);
        $this->assertSame('2024-05-10 13:45:00', $iso->toDateTimeString());
    }

    public function test_it_casts_json_strings_and_already_decoded_arrays(): void
    {
        $this->assertSame(['a' => 1], $this->caster->cast('{"a":1}', 'array'));
        $this->assertSame(['a' => 1], $this->caster->cast(['a' => 1], 'array'));

        $collection = $this->caster->cast(['a', 'b'], 'collection');

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame(['a', 'b'], $collection->all());
    }

    public function test_it_casts_backed_enums(): void
    {
        $this->assertSame(Status::Published, $this->caster->cast('published', Status::class));
    }

    public function test_it_does_not_reuse_cached_custom_cast_values(): void
    {
        $this->assertSame(['first'], $this->caster->cast('first', WrapCast::class));
        $this->assertSame(['second'], $this->caster->cast('second', WrapCast::class));
    }
}
