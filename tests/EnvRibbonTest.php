<?php

namespace Perspikapps\LaravelEnvRibbon\Tests;

use Orchestra\Testbench\TestCase;
use Perspikapps\LaravelEnvRibbon\EnvRibbon;
use Perspikapps\LaravelEnvRibbon\EnvRibbonServiceProvider;
use Perspikapps\LaravelEnvRibbon\Facades\EnvRibbon as EnvRibbonFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EnvRibbonTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [EnvRibbonServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'EnvRibbon' => EnvRibbonFacade::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('env-ribbon.enabled', true);
        $app['config']->set('env-ribbon.environments', [
            'testing' => [
                'visible' => true,
                'color' => 'crimson',
            ],
            '*' => [
                'visible' => true,
                'color' => 'black',
            ],
        ]);
    }

    public function test_service_provider_registers_envribbon()
    {
        $envribbon = $this->app->make(EnvRibbon::class);
        $this->assertInstanceOf(EnvRibbon::class, $envribbon);
    }

    public function test_facade_resolves_correctly()
    {
        $this->assertInstanceOf(EnvRibbon::class, EnvRibbonFacade::getFacadeRoot());
    }

    public function test_is_enabled_returns_true_when_configured()
    {
        $this->app['config']->set('env-ribbon.enabled', true);
        $envribbon = new EnvRibbon($this->app, $this->app->make('AvtoDev\AppVersion\AppVersionManagerInterface'));
        $envribbon->enable();
        $this->assertTrue($envribbon->isEnabled());
    }

    public function test_is_enabled_returns_false_when_disabled_in_config()
    {
        $this->app['config']->set('env-ribbon.enabled', false);
        // Make a fresh instance after changing config
        $envribbon = new EnvRibbon($this->app, $this->app->make('AvtoDev\AppVersion\AppVersionManagerInterface'));
        $this->assertFalse($envribbon->isEnabled());
    }

    public function test_enable_forces_enabled_state()
    {
        $this->app['config']->set('env-ribbon.enabled', false);
        $envribbon = new EnvRibbon($this->app, $this->app->make('AvtoDev\AppVersion\AppVersionManagerInterface'));
        $envribbon->enable();
        $this->assertTrue($envribbon->isEnabled());
    }

    public function test_load_config_handles_null_environments_gracefully()
    {
        $this->app['config']->set('env-ribbon.environments', null);
        // Should not throw
        $envribbon = new EnvRibbon($this->app, $this->app->make('AvtoDev\AppVersion\AppVersionManagerInterface'));
        $this->assertNotNull($envribbon);
    }

    public function test_modify_response_injects_ribbon_into_html()
    {
        $envribbon = $this->app->make(EnvRibbon::class);
        $envribbon->enable();

        $html = '<html><head></head><body><p>Hello</p></body></html>';
        $request = Request::create('/');
        $response = new Response($html);

        $envribbon->modifyResponse($request, $response);

        $content = $response->getContent();
        $this->assertStringContainsString('<style>', $content);
        $this->assertStringContainsString('env-ribbon', $content);
    }

    public function test_modify_response_does_not_inject_when_disabled()
    {
        $this->app['config']->set('env-ribbon.enabled', false);
        $envribbon = new EnvRibbon($this->app, $this->app->make('AvtoDev\AppVersion\AppVersionManagerInterface'));

        $html = '<html><head></head><body><p>Hello</p></body></html>';
        $request = Request::create('/');
        $response = new Response($html);

        $envribbon->modifyResponse($request, $response);

        $this->assertEquals($html, $response->getContent());
    }
}
