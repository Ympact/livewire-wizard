<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            $this->makeACleanSlate();
        });

        $this->beforeApplicationDestroyed(function () {
            $this->makeACleanSlate();
        });

        parent::setUp();
    }

    /**
     * Register package service providers required for tests (Livewire).
     */
    protected function getPackageProviders($app)
    {
        return [
            \Livewire\LivewireServiceProvider::class,
            \Ympact\Wizard\WizardServiceProvider::class,
        ];
    }

    public function makeACleanSlate()
    {
        Artisan::call('view:clear');

        // File::deleteDirectory($this->livewireViewsPath());
        // File::deleteDirectory($this->livewireClassesPath());
        // File::deleteDirectory($this->livewireTestsPath());
        File::delete(app()->bootstrapPath('cache/livewire-components.php'));
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('view.paths', [
            __DIR__.'/resources/views',
            resource_path('views'),
        ]);

        // Ensure the default database uses sqlite in-memory for tests unless overridden
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
        // Set an application key required by encryption and some packages
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        // Add tests/resources/views to the view paths so package test views are discovered
    }
}
