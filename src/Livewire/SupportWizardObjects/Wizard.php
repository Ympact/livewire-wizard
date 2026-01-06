<?php

namespace Ympact\Wizard\Livewire\SupportWizardObjects;

use Livewire\Component;
use Illuminate\Support\Collection;
use Ympact\Wizard\Livewire\SupportWizardObjects\Values\StepDetails;

class Wizard
{
    /**
     * Collection of StepDetails describing discovered steps (numeric keys).
     *
     * @var Collection<int, StepDetails>
     */
    public Collection $steps;

    /**
     * The current step property name on the component.
     */
    public ?string $currentStep = null;

    public function __construct(
        protected Component $component,
        protected string $propertyName
    ) {
        $this->steps = new \Illuminate\Support\Collection();
    }

    public function getComponent(): Component
    {
        return $this->component;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    /**
     * Resolve a `Step` instance from various identifiers.
     *
     * Accepted inputs are:
     * - an actual `Step` instance (returned directly),
     * - a `StepDetails` object (the step property name will be used),
     * - a numeric index (0-based) into the discovered steps,
     * - a property name (public property on the component), or
     * - a step class FQCN registered in `allSteps()`.
     *
     * @param  int|string|StepDetails|Step  $step  The step identifier to resolve.
     * @return Step|null  The resolved `Step` instance or null when not found.
     */
    public function getStep(int|string|StepDetails|Step $step): ?Step
    {
        if ($step instanceof Step) {
            // if the input is a Step object, return it directly
            return $step;
        }

        if ($step instanceof StepDetails) {
            return $this->{$step->name};
        }

        // get the value of the steps array by the index or slug
        if (is_numeric($step)) {
            $keys = array_keys($this->steps->toArray());
            $idx = (int) $step;
            if (! array_key_exists($idx, $keys)) {
                return null;
            }

            $classKey = $keys[$idx];

            return $this->{$this->steps[$classKey]} ?? null;
        }
        elseif (is_string($step)) {
            // first try if $step is a property name already
            if (property_exists($this, $step)) {
                return $this->{$step};
            }
            // then try if $step is a class name
            if (array_key_exists($step, $this->steps->toArray())) {
                return $this->{$this->steps[$step]};
            }
        }

        // if the step is not found, return null
        return null;
    }

}
