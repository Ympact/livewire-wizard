<?php

namespace Ympact\Wizard;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class WizardServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/ympact/wizard'),
        ], 'wizard-views');

        // Register the commands
        $this->bootCommands();
    }

    public function bootCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        AboutCommand::add('Ympact Wizard', fn () => ['Version' => '0.0.1']);

        $this->commands([

        ]);
    }
}
