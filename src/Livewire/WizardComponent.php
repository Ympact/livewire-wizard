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
        } else {
            return $this->{$this->allSteps[$step]} ?? null;
        }
    }

    public function getStepName(int|Step|string|StepDetails $step) : ?string
    {
        if($step instanceOf StepDetails){
            return $step->name;
        }
        if(is_string($step)){
            // first try if $step is a property name already
            if(property_exists($this, $step) && (new $step()) instanceof Step){
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

        return null;
    }

    public function getPreviousStep(string|Step $currentStep) : ?Step
    {
        $currentIndex = $this->getStepIndex($currentStep);

        if ($currentIndex === false || $currentIndex <= 0) {
            return null; // No previous step or current step is the first one
        }
        $previousIndex = $currentIndex - 1;
        return $this->getStep($previousIndex) ?? null;
    }

    public function getNextStep($currentStep) : ?Step
    {
        $currentIndex = $this->getStepIndex($currentStep);

        if ($currentIndex === false || $currentIndex >= count($this->allSteps) - 1) {
            return null; // No next step or current step is the last one
        }
        $nextIndex = $currentIndex + 1;
        return $this->getStep($nextIndex) ?? null;
    }

    public function getLastStep() : ?Step
    {
        $allSteps = $this->allSteps;
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

            // if there is a method `stepDetails` in the component, use it to get the details
            $details = method_exists($this, 'stepDetails') ? $this->stepDetails() : [];
            
            return new StepDetails(
                key: $name,
                name: $name,
                class: $stepClass,
                index: $this->getStepIndex($stepClass),
                title: Arr::get($details, "{$name}.title", $step->stepTitle ?? $name),
                description: Arr::get($details, "{$name}.description", $step->stepDescription ?? null),
                icon: Arr::get($details, "{$name}.icon", $step->stepIcon ?? null),
            );
            
        });
    }

    #[Computed]
    public function allSteps()
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
}