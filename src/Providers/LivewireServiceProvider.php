<?php

namespace Ympact\Wizard\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\ComponentHookRegistry;
use Livewire\Livewire;
use Ympact\Wizard\Livewire\SupportStepObjects;


class LivewireServiceProvider extends ServiceProvider
{

    public function boot(): void
    {

        foreach ([
            SupportStepObjects\StepObjectSynth::class,
        ] as $synth) {
            Livewire::propertySynthesizer($synth);
        }

        foreach ([
            SupportStepObjects\SupportStepObjects::class,
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
