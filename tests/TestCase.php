<?php

declare(strict_types=1);

namespace Lionix\CastableRequest\Tests;

use Lionix\CastableRequest\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [ServiceProvider::class];
    }
}
