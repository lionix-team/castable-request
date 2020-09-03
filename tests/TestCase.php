<?php

namespace Lionix\CastableRequest\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * @inheritDoc
     */
    protected function getPackageProviders($app)
    {
        return [
            \Lionix\CastableRequest\ServiceProvider::class,
        ];
    }
}
