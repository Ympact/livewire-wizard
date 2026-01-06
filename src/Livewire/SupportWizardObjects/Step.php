<?php

namespace Ympact\Wizard\Livewire\SupportWizardObjects;

use Illuminate\Validation\ValidationException;
use Livewire\Form;
use Ympact\Wizard\Livewire\SupportWizardObjects\Values\StepDetails;

/**
 * @method \Ympact\Wizard\Livewire\Components\WizardComponent getComponent()
 */
abstract class Step extends Form
{
    public function enabled(): bool
    {
        return true;
    }

    public function visible(): bool
    {
        return true;
    }
    
    /**
     * Validation rules for the step form.
     *
     * @return array<string,mixed>
     */
    protected function rules(): array
    {
        return [];
    }

    public function isValid(): bool
    {
        // if the step has rules defined, we can validate it
        // otherwise, we assume it's valid by default
        if (empty($this->rules())) {
            return true;
        }
        try {
            $this->validate();

            return true;
        } catch (ValidationException $e) {
            return false;
        }
    }

    public function getStep(int|string|StepDetails|Step $step): ?Step
    {
        return $this->getComponent()->getStep($step);
    }

    public function previousStep(): ?Step
    {
        return $this->getComponent()->getPreviousStep($this);
    }

    public function nextStep(): ?Step
    {
        return $this->getComponent()->getNextStep($this);
    }

    public function getLastStep(): ?Step
    {
        return $this->getComponent()->getLastStep();
    }

    public function getFirstStep(): ?Step
    {
        return $this->getComponent()->getFirstStep();
    }

    public function getOwnIndex(): int
    {
        return $this->getComponent()->getStepIndex($this);
    }
}
