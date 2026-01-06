<?php

namespace Ympact\Wizard\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\ComponentHookRegistry;
use Livewire\Livewire;
use Ympact\Wizard\Livewire\SupportWizardObjects;

class LivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        foreach ([
            SupportWizardObjects\StepObjectSynth::class,
        ] as $synth) {
            Livewire::propertySynthesizer($synth);
        }

        foreach ([
            SupportWizardObjects\SupportWizardObjects::class,
        ] as $feature) {
            Livewire::componentHook($feature);
        }

        ComponentHookRegistry::boot();

    }

    public function register(): void
    {
        foreach ([
            LivewireServiceProvider::class,
        ] as $provider) {
            $this->app->register($provider);
        }
    }
}
