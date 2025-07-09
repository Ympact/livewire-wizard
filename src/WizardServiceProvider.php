<?php

namespace Ympact\Wizard;

use Illuminate\Support\ServiceProvider;

class WizardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge the package configuration with the application's copy.
        $this->mergeConfigFrom(__DIR__.'/../config/wizard.php', 'wizard');
    }

    public function boot(): void
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__.'/../config/wizard.php' => config_path('wizard.php'),
        ], 'wizard-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/ympact/wizard'),
        ], 'wizard-views');

        // Register the commands
        $this->bootCommands();
    }
    
    public function bootCommands()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([

        ]);
    }
}