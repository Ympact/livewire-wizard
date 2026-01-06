<?php

namespace Ympact\Wizard\Livewire\Components;

use Livewire\Component;
use Ympact\Wizard\Livewire\SupportWizardObjects\SimpleWizardTrait;
/**
 * @property array<string,string> $allSteps
 * @property \Illuminate\Support\Collection<int, \Ympact\Wizard\Livewire\SupportWizardObjects\Values\StepDetails> $steps
 */
class WizardComponent extends Component
{
    use SimpleWizardTrait;

    /**
    * Mount the component and set the initial current step.
    *
    * If no current step has been set, this initializes it to the
    * first discovered step index (0).
    *
    * @return void
     */
    public function mount()
    {
        // Initialize the current tab to the first tab if not set
        if (is_null($this->currentStep)) {
            $this->currentStep = $this->getStepName(0);
        }
    }


}
