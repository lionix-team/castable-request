<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Tests\Unit;

use Lionix\CastableRequest\InMemoryCastsRegistry;
use PHPUnit\Framework\TestCase;

class InMemoryCastsRegistryTest extends TestCase
{
    public function test_it_registers_and_returns_casts(): void
    {
        $registry = new InMemoryCastsRegistry;

        $registry->register('created_after', 'date');
        $registry->register('active', 'boolean');

        $this->assertSame(['created_after' => 'date', 'active' => 'boolean'], $registry->all());
    }

    public function test_it_overrides_an_existing_cast(): void
    {
        $registry = new InMemoryCastsRegistry;

        $registry->register('active', 'boolean');
        $registry->register('active', 'integer');

        $this->assertSame(['active' => 'integer'], $registry->all());
    }

    public function test_it_forgets_a_cast(): void
    {
        $registry = new InMemoryCastsRegistry;

        $registry->register('active', 'boolean');
        $registry->forget('active');
        $registry->forget('missing');

        $this->assertSame([], $registry->all());
    }
}
