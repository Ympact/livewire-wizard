<?php

namespace Ympact\Wizard;

use Illuminate\Support\ServiceProvider;
use Livewire\ComponentHookRegistry;
use Livewire\Livewire;

use function Livewire\on;

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


        foreach ([
            \Ympact\Wizard\Livewire\SupportStepObjects\SupportStepObjects::class,
        ] as $feature) {
            $this->addLivewireFeature($feature);
        }


        // Register the commands
        $this->bootCommands();
    }
    
    protected function addLivewireFeature($feature){

        Livewire::componentHook($feature);
        
        on('mount', function (...$args) use ($feature) {
            // in livewire v3 we have $component, $params, $key, $parent
            // in livewire v4 we have $component, $params, $key, $parent, $attributes
            // normalize to livewire v4 signature
            if(count($args) === 4){ 
                array_push($args, null);
            };

            [$component, $params, $key, $parent, $attributes] = $args;
            
            if (! $feature = ComponentHookRegistry::initializeHook($feature, $component)) {
                return;
            }

            $feature->callBoot();
            $feature->callMount($params, $parent, $attributes);
        });

        on('hydrate', function ($component, $memo) use ($feature) {
            if (! $feature = ComponentHookRegistry::initializeHook($feature, $component)) {
                return;
            }

            $feature->callBoot();
            $feature->callHydrate($memo);
        });
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