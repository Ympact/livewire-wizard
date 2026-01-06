<?php

namespace Ympact\Wizard\Livewire\SupportWizardObjects;

use Livewire\ComponentHook;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Ympact\Wizard\Livewire\SupportWizardObjects\Values\StepDetailsSynth;

class SupportWizardObjects extends ComponentHook
{
    public static function provide(): void
    {
        foreach([
            WizardObjectSynth::class,
            StepObjectSynth::class,
            StepDetailsSynth::class
        ] as $synth){
            app('livewire')->propertySynthesizer(
                $synth
            );
        }
    }

    public function boot(): void
    {
        $this->initializeObjects();
    }

    protected function initializeObjects(): void
    {
        foreach ((new ReflectionClass($this->component))->getProperties() as $property) {
            // Public properties only...
            if ($property->isPublic() !== true) {
                continue;
            }
            // Uninitialized properties only...
            if ($property->isInitialized($this->component)) {
                continue;
            }

            $type = $property->getType();

            if (! $type instanceof ReflectionNamedType) {
                continue;
            }

            $typeName = $type->getName();

            // "Wizard" object
            if (is_subclass_of($typeName, Wizard::class)) {
                $this->initializeWizardObjects($property, $typeName);
            }

            // "Step" object
            if (is_subclass_of($typeName, Step::class)) {
                $this->initializeStepObjects($property, $typeName);
            }

            
        }
    }

    public function initializeWizardObjects(ReflectionProperty $property, string $typeName): void
    {
        $wizard = new $typeName(
            $this->component,
            $name = $property->getName()
        );

        $callBootMethod = WizardObjectSynth::bootWizardObject($this->component, $wizard, $name);

        $property->setValue($this->component, $wizard);

        $callBootMethod();
    }

    public function initializeStepObjects(ReflectionProperty $property, string $typeName): void
    {
        $step = new $typeName(
            $this->component,
            $name = $property->getName()
        );

        $callBootMethod = StepObjectSynth::bootStepObject($this->component, $step, $name);

        $property->setValue($this->component, $step);

        $callBootMethod();
    }
}
