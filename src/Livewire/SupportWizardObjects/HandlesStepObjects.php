<?php

namespace Ympact\Wizard\Livewire\SupportWizardObjects;

/**
 * Helpers for working with discovered Step objects.
 */
trait HandlesStepObjects
{
    /**
     * Return an associative array of step class FQCN => component property name.
     *
     * @return array<string,string>
     */
    public function getStepObjects(): array
    {
        $steps = [];

        // Use the component's `allSteps()` method to obtain the mapping.
        $all = $this->allSteps();

        foreach ($all as $class => $name) {
            if (is_string($class) && is_string($name)) {
                $steps[$class] = $name;
            }
        }

        return $steps;
    }
}
