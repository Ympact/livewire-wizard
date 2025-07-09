<?php

namespace Ymapct\Wizard\Livewire\SupportStepObjects;

use Illuminate\Validation\ValidationException;
use Livewire\Form;
use Ymapct\Wizard\DTO\StepDetails;

abstract class Step extends Form
{

    public function enabled(){
        return true;
    }

    public function visible(){
        return true;
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
        $allSteps = $this->getComponent()->getAllSteps();
        return end($allSteps);
    }

    public function getFirstStep(): ?Step
    {
        $allSteps = $this->getComponent()->getAllSteps();
        return reset($allSteps);
    }

    public function getOwnIndex(): int
    {
        return $this->getComponent()->getStepIndex($this);
    }


}
