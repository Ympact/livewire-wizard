<?php

namespace Ympact\Wizard\Livewire;

use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Ympact\Wizard\DTO\StepDetails;
use Ympact\Wizard\Livewire\SupportStepObjects\Step;
use ReflectionClass;
use ReflectionProperty;

class WizardComponent extends Component
{
    /**
     * @var string $currentStep
     */
    public $currentStep = null;

    /**
     * We mount the initial step.
     * @return void
     */
    public function mount(){
        // Initialize the current tab to the first tab if not set
        if (is_null($this->currentStep)) {
            $this->currentStep = $this->getStepName(0);
        }
    }

    /**
     * Get a step by its index, property name, StepDetails object or Step object itself.
     * If the input is a Step object, it returns that object directly.
     *
     * @param int|string|StepDetails|Step $step
     * @return Step|null
     */
    public function getStep(int|string|StepDetails|Step $step) : ?Step
    {
        if ($step instanceof Step) {
            // if the input is a Step object, return it directly
            return $step;
        }

        if($step instanceOf StepDetails){
            return $this->{$step->name};
        } 

        // get the value of the steps array by the index or slug
        if (is_numeric($step)) {
            return $this->{$this->allSteps[array_keys( $this->allSteps )[$step] ]} ?? null;
        } 
        if(is_string($step)){
            // first try if $step is a property name already
            if(property_exists($this, $step)){
                return $this->{$step};
            }
            // then try if $step is a class name
            if(array_key_exists($step, $this->allSteps)){
                return $this->{$this->allSteps[$step]};
            } 
        }
        // if the step is not found, return null
        return null;
    }

    /**
     * Get the property name of a step by its index, property name, StepDetails object or Step object itself.
     * If the input is a StepDetails object, it returns the name directly.
     *
     * @param int|Step|string|StepDetails $step
     * @return string|null
     */
    public function getStepName(int|Step|string|StepDetails $step) : ?string
    {
        if($step instanceOf StepDetails){
            return $step->name;
        }
        if(is_string($step)){
            // first try if $step is a property name already
            if(property_exists($this, $step)){
                return $step;
            }
            // then try if $step is a class name
            if(array_key_exists($step, $this->allSteps)){
                return $this->allSteps[$step];
            } 
        }
        if(is_numeric($step)){
            return $this->allSteps[array_keys( $this->allSteps )[$step]];
        }
        if($step instanceof Step){
            return $this->allSteps[$step::class];
        }

        return null;
    }

    /**
     * Get the previous step based on the current step.
     * If the current step is not provided, it uses the currentStep property.
     * If there is no previous step, it returns null.
     * @param string|Step|null $currentStep
     * @return Step|null
     */
    public function getPreviousStep(mixed $currentStep = null) : ?Step
    {
        $currentStep = $currentStep ?? $this->currentStep;
        $currentIndex = $this->getStepIndex($currentStep);

        if ($currentIndex === false || $currentIndex <= 0) {
            return null; // No previous step or current step is the first one
        }
        $previousIndex = $currentIndex - 1;
        return $this->getStep($previousIndex) ?? null;
    }

    /**
     * get the next step based on the current step.
     * If the current step is not provided, it uses the currentStep property.
     * If there is no next step, it returns null.
     * @param string|Step|null $currentStep
     * @return Step|null
     */
    public function getNextStep($currentStep = null) : ?Step
    {
        $currentStep = $currentStep ?? $this->currentStep;
        $currentIndex = $this->getStepIndex($currentStep);

        if ($currentIndex === false || $currentIndex >= count($this->allSteps) - 1) {
            return null; // No next step or current step is the last one
        }
        $nextIndex = $currentIndex + 1;
        return $this->getStep($nextIndex) ?? null;
    }

    /**
     * Get the last step in the wizard.
     * If $accessible is true, it will return the last step that is enabled and visible.
     * If no steps are available, it returns null.
     *
     * @param bool $accessible
     * @return Step|null
     */
    public function getLastStep($accessible = false) : ?Step
    {
        $allSteps = $this->getAllSteps($accessible);
        if (empty($allSteps)) {
            return null; // No steps available
        }
        $lastStepClass = array_key_last($allSteps);
        return $this->getStep($lastStepClass);
    }

    public function getFirstStep() : ?Step
    {
        $allSteps = $this->allSteps;
        if (empty($allSteps)) {
            return null; // No steps available
        }
        $firstStepClass = array_key_first($allSteps);
        return $this->getStep($firstStepClass);
    }

    public function getStepIndex(Step|string $step) : int
    {
        $stepClass = $step instanceof Step ? $step::class : $step;
        $index = array_search($stepClass, array_keys($this->allSteps));
        return $index !== false ? $index : -1; // Return -1 if not found
    }

    public function isEnabled(Step|string|StepDetails $step) : bool
    {
        $step = $this->getStep($step);

        if (method_exists($step, 'enabled')) {
            return $step->enabled();
        }

        return true; // Default to true if not found or no enabled method
    }

    public function isVisible(Step|string|StepDetails $step) : bool
    {
        $step = $this->getStep($step);

        if (method_exists($step, 'visible')) {
            return $step->visible();
        }

        return true; // Default to false if not found or no visible method
    }

    public function isValid(Step|string|StepDetails $step) : bool
    {
        $step = $this->getStep($step);

        if (method_exists($step, 'isValid')) {
            return $step->isValid();
        }
        
        // Default to true if no isValid method exists
        return true;
    }

    public function getView(Step|string|StepDetails $step)
    {
        $step = $this->getStep($step);

        if (method_exists($step, 'render')) {
            return $step->render();
        }

        return null;
        // todo:
        // determine the view based on the step class name
        //$stepClass = is_string($step) ? $step : get_class($step);
        //$viewName = str_replace('\\', '.', $stepClass);
        //return "livewire.{$viewName}";
    }

    public function getStepDetails(Step|string|int|StepDetails $step) : ?StepDetails
    {
        if($step instanceOf StepDetails){
            return $step;
        }
        $name = $this->getStepName($step);
        return $this->steps->where('name', $name )->first();
    }

    /**
     * is the step the last (accessible) step?
     * This checks if the step is the last step in the allSteps array.
     * It does not check if the step is accessible or visible.
     * If $accesible is true, it will check if it is the last visible and enabled step.
     * 
     * @param Step|string|StepDetails $step
     * @param bool $accesible
     * @return bool
     */
    public function isLastStep(Step|string|StepDetails $step, $accesible = false) : bool
    {
        $step = $this->getStep($step);
        if (!$step) {
            return false; // Step not found
        }

        // Get the last step in the allSteps array
        $lastStep = $this->getLastStep();
        if (!$lastStep) {
            return false; // No last step found
        }

        // Compare the current step with the last step
        return $step::class === $lastStep::class;
    }

    // gotoNextStep
    public function gotoNextStep($dispatch = false) : ?Step
    {
        $currentStep = $this->getStep($this->currentStep);
        if (!$currentStep) {
            return null; // No current step found
        }

        $nextStep = $this->getNextStep($currentStep);

        return $nextStep ? $this->gotoStep($nextStep, $dispatch) : null;
    }

    // gotoPreviousStep
    public function gotoPreviousStep($dispatch = false) : ?Step
    {
        $currentStep = $this->getStep($this->currentStep);
        if (!$currentStep) {
            return null; // No current step found
        }

        $previousStep = $this->getPreviousStep($currentStep);

        return $previousStep ? $this->gotoStep($previousStep, $dispatch) : null;
    }

    // gotoStep
    public function gotoStep(Step|int|string|StepDetails $step, $dispatch = false) : ?Step
    {
        $step = $this->getStep($step);
        if (!$step || !$this->isEnabled($step) || !$this->isVisible($step)) {
            return null; // Step not found or not accessible
        }

        // Update current step to the specified step
        $this->currentStep = $this->getStepName($step);
        if ($dispatch) {
            $this->dispatch('stepChanged', ['step' => $step]);
        }
        return $step;
    }

    // gotoFirstStep
    public function gotoFirstStep($dispatch = false) : ?Step
    {
        $firstStep = $this->getFirstStep();
        if (!$firstStep || !$this->isEnabled($firstStep) || !$this->isVisible($firstStep)) {
            return null; // No first step or not accessible
        }

        return $this->gotoStep($firstStep, $dispatch);
    }

    // gotoLastStep
    public function gotoLastStep($dispatch = false, $accessible = true) : ?Step
    {
        $lastStep = $this->getLastStep($accessible);
        if (!$lastStep || !$this->isEnabled($lastStep) || !$this->isVisible($lastStep)) {
            return null; // No last step or not accessible
        }

        return $this->gotoStep($lastStep, $dispatch);
    }

    // gotoFirstInvalidStep
    public function gotoFirstInvalidStep() : ?Step
    {
        // Iterate through all steps to find the first invalid step
        foreach ($this->steps as $step) {
            if (!$this->isValid($step)) {
                return $this->gotoStep($step);
            }
        }

        return null; // All steps are valid
    }

    // hasNextStep (visible)
    public function hasNextStep() : bool
    {
        $currentStep = $this->getStep($this->currentStep);
        if (!$currentStep) {
            return false; // No current step found
        }

        $nextStep = $this->getNextStep($currentStep);
        return $nextStep && $this->isVisible($nextStep); // && $this->isEnabled($nextStep)
    }

    // hasPreviousStep (visible)
    public function hasPreviousStep() : bool
    {
        $currentStep = $this->getStep($this->currentStep);
        if (!$currentStep) {
            return false; // No current step found
        }

        $previousStep = $this->getPreviousStep($currentStep);
        return $previousStep && $this->isVisible($previousStep); // && $this->isEnabled($previousStep)
    }


    public function isAllValid() : bool
    {
        // Check if all steps are valid
        return $this->steps->every(function ($step) {
            return $this->isValid($step);
        });
    }

    #[Computed]
    public function steps()
    {
        // get title, description, icon, initial state, etc. of the steps
        return collect($this->allSteps)->map(function ($name, $stepClass) {
            $step = $this->{$name};
            $name = (string) $name; // Ensure $name is a string
            $index = $this->getStepIndex($stepClass);

            // if there is a method `stepDetails` in the component, use it to get the details
            $details = method_exists($this, 'stepDetails') ? $this->stepDetails() : [];
            
            // if there is a method for custom keys use that
            $marker = method_exists($this, 'stepMarker') ? $this->stepMarker($index + 1, $name) : null;
            $marker = $marker ?? Arr::get($details, "{$name}.marker", $step->stepMarker ?? $index + 1); // Fallback to index if marker is not defined

            return new StepDetails(
                index: $this->getStepIndex($stepClass),
                name: $name,
                class: $stepClass,
                marker: $marker,
                title: Arr::get($details, "{$name}.title", $step->stepTitle ?? $name),
                description: Arr::get($details, "{$name}.description", $step->stepDescription ?? null),
                icon: Arr::get($details, "{$name}.icon", $step->stepIcon ?? null),
            );
            
        });
    }

    #[Computed]
    public function allSteps() : array
    {
        $steps = [];
        $reflection = new ReflectionClass($this);

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $type = $property->getType();
            $className = $type ? (string)$type : null;
            if ($type && class_exists($className)) {
                if (is_subclass_of($className, Step::class)) {
                    $name = $property->getName();
                    $steps[$className] = $name;
                }
            }
        }

        return $steps;
    }

    /**
     * Get all steps
     * Optionally filter them based on accessibility, visibility, enabled state, and validity.
     * @param mixed $accessible
     * @param mixed $enabled
     * @param mixed $visible
     * @param mixed $valid
     * @return array
     */
    public function getAllSteps($accessible = false, $enabled = false, $visible = false, $valid = false) : array
    {
        $allSteps = $this->allSteps;

        if($accessible) {
            $visible = true;
            $enabled = true;
        }

        if ($enabled || $visible || $valid) {
            // filter out the steps that are not enabled or visible
            $allSteps = array_filter($allSteps, function($stepClass) use ($enabled, $visible, $valid) {
                $step = $this->getStep($stepClass);
                return (!$enabled || $this->isEnabled($step)) 
                    && (!$visible || $this->isVisible($step))
                    && (!$valid || $this->isValid($step));
            });
        }
        return $allSteps;
    }
}