<?php

namespace Mumble\MBurger\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return string[]
     */
    protected function getPackageProviders($app)
    {
        return ['Mumble\MBurger\MBurgerServiceProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mburger.api_key', env('MBURGER_API_KEY', '0e3362a17c74a43ec57cc160ca6d222fad79c5ee'));
        $app['config']->set('mburger.api_version', '3');
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }
}
