<?php

namespace Perspikapps\LaravelEnvRibbon\Facades;

use Illuminate\Support\Facades\Facade;
use Perspikapps\LaravelEnvRibbon\EnvRibbon as EnvRibbonService;

class EnvRibbon extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return EnvRibbonService::class;
    }
}
