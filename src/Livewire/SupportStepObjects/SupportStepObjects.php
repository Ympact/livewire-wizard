<?php

namespace Ympact\Wizard\Livewire\SupportStepObjects;

use ReflectionClass;
use Livewire\ComponentHook;
use ReflectionNamedType;

class SupportStepObjects extends ComponentHook
{
    public static function provide()
    {
        app('livewire')->propertySynthesizer(
            StepObjectSynth::class
        );
    }

    function boot()
    {
        $this->initializeStepObjects();
    }

    protected function initializeStepObjects()
    {
        foreach ((new ReflectionClass($this->component))->getProperties() as $property) {
            // Public properties only...
            if ($property->isPublic() !== true) continue;
            // Uninitialized properties only...
            if ($property->isInitialized($this->component)) continue;

            $type = $property->getType();

            if (! $type instanceof ReflectionNamedType) continue;

            $typeName = $type->getName();

            // "Step" object property types only...
            if (! is_subclass_of($typeName, Step::class)) continue;

            $step = new $typeName(
                $this->component,
                $name = $property->getName()
            );

            $callBootMethod = StepObjectSynth::bootStepObject($this->component, $step, $name);

            $property->setValue($this->component, $step);

            $callBootMethod();
        }
    }
}
