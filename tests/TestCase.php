<?php

namespace Perspikapps\LaravelEnvRibbon\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Perspikapps\LaravelEnvRibbon\EnvRibbonServiceProvider;
use Perspikapps\LaravelEnvRibbon\Facades\EnvRibbon as EnvRibbonFacade;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [EnvRibbonServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'EnvRibbon' => EnvRibbonFacade::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('env-ribbon.enabled', true);
        $app['config']->set('env-ribbon.environments', [
            'testing' => ['visible' => true, 'color' => 'crimson'],
            '*' => ['visible' => true, 'color' => 'black'],
        ]);
    }
}
