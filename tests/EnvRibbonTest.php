<?php

namespace Perspikapps\LaravelEnvRibbon\Tests;

use Orchestra\Testbench\TestCase;
use Perspikapps\LaravelEnvRibbon\EnvRibbonServiceProvider;

class EnvRibbonTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [EnvRibbonServiceProvider::class];
    }

    public function test_example()
    {
        $this->assertEquals(1, 1);
    }
}
