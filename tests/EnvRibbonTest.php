<?php

use Perspikapps\LaravelEnvRibbon\EnvRibbon;
use Perspikapps\LaravelEnvRibbon\Facades\EnvRibbon as EnvRibbonFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

test('service provider registers envribbon', function () {
    expect($this->app->make(EnvRibbon::class))->toBeInstanceOf(EnvRibbon::class);
});

test('facade resolves correctly', function () {
    expect(EnvRibbonFacade::getFacadeRoot())->toBeInstanceOf(EnvRibbon::class);
});

test('isEnabled returns true when enabled', function () {
    $envribbon = new EnvRibbon($this->app, $this->app->make('AvtoDev\AppVersion\AppVersionManagerInterface'));
    $envribbon->enable();

    expect($envribbon->isEnabled())->toBeTrue();
});

test('isEnabled returns false when disabled in config', function () {
    $this->app['config']->set('env-ribbon.enabled', false);
    $envribbon = new EnvRibbon($this->app, $this->app->make('AvtoDev\AppVersion\AppVersionManagerInterface'));

    expect($envribbon->isEnabled())->toBeFalse();
});

test('enable forces enabled state regardless of config', function () {
    $this->app['config']->set('env-ribbon.enabled', false);
    $envribbon = new EnvRibbon($this->app, $this->app->make('AvtoDev\AppVersion\AppVersionManagerInterface'));
    $envribbon->enable();

    expect($envribbon->isEnabled())->toBeTrue();
});

test('loadConfig handles null environments gracefully', function () {
    $this->app['config']->set('env-ribbon.environments', null);
    $envribbon = new EnvRibbon($this->app, $this->app->make('AvtoDev\AppVersion\AppVersionManagerInterface'));

    expect($envribbon)->toBeInstanceOf(EnvRibbon::class);
});

test('modifyResponse injects ribbon into html', function () {
    $envribbon = $this->app->make(EnvRibbon::class);
    $envribbon->enable();

    $html = '<html><head></head><body><p>Hello</p></body></html>';
    $response = new Response($html);

    $envribbon->modifyResponse(Request::create('/'), $response);

    expect($response->getContent())
        ->toContain('<style>')
        ->toContain('env-ribbon');
});

test('modifyResponse does not inject when disabled', function () {
    $this->app['config']->set('env-ribbon.enabled', false);
    $envribbon = new EnvRibbon($this->app, $this->app->make('AvtoDev\AppVersion\AppVersionManagerInterface'));

    $html = '<html><head></head><body><p>Hello</p></body></html>';
    $response = new Response($html);

    $envribbon->modifyResponse(Request::create('/'), $response);

    expect($response->getContent())->toBe($html);
});
