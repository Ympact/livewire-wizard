<?php

namespace Ympact\Wizard\Livewire\SupportWizardObjects;

use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;
use ReflectionClass;
use ReflectionProperty;
use Ympact\Wizard\Livewire\SupportWizardObjects\Values\StepDetails;

/**
 * @todo create a simple version of the wizard trait that only has the basic step navigation
 */
trait SimpleWizardTrait
{
    use HandlesStepObjects;

    /**
     * @var string|null
     */
    public $currentStep = null;

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
            $keys = array_keys($this->allSteps);
            $idx = (int) $step;
            if (! array_key_exists($idx, $keys)) {
                return null;
            }

            $classKey = $keys[$idx];

            return $this->{$this->allSteps[$classKey]} ?? null;
        }
        elseif (is_string($step)) {
            // first try if $step is a property name already
            if (property_exists($this, $step)) {
                return $this->{$step};
            }
            // then try if $step is a class name
            if (array_key_exists($step, $this->allSteps)) {
                return $this->{$this->allSteps[$step]};
            }
        }

        // if the step is not found, return null
        return null;
    }

    /**
     * Determine whether the given step is accessible.
     *
     * A step is considered accessible when it exists and both `isEnabled`
     * and `isVisible` return true for it.
     *
     * @param  Step|string|StepDetails|int  $step  Step identifier or instance.
     * @return bool
     */
    public function isAccessible(Step|string|StepDetails|int $step): bool
    {
        $step = $this->getStep($step);
        if (! $step) {
            return false; // Step not found
        }

        // Check if the step is enabled and visible
        return (bool) $this->isEnabled($step) && (bool) $this->isVisible($step);
    }

    /**
     * Resolve the component property name for a step identifier.
     *
     * Accepts the same identifiers as `getStep()` and returns the
     * corresponding public property name on the component (e.g. "myStep").
     *
     * @param  int|Step|string|StepDetails  $step  Step identifier.
     * @return string|null  The property name or null when it cannot be resolved.
     */
    public function getStepName(int|Step|string|StepDetails $step): ?string
    {
        if ($step instanceof StepDetails) {
            return $step->name;
        }
        if (is_string($step)) {
            // first try if $step is a property name already
            if (property_exists($this, $step)) {
                return $step;
            }
            // then try if $step is a class name
            if (array_key_exists($step, $this->allSteps)) {
                return $this->allSteps[$step];
            }
        }
        if (is_numeric($step)) {
            $keys = array_keys($this->allSteps);
            $idx = (int) $step;
            if (! array_key_exists($idx, $keys)) {
                return null;
            }

            return $this->allSteps[$keys[$idx]];
        }
        if ($step instanceof Step) {
            return $this->allSteps[$step::class];
        }

        return null;
    }

    /**
     * Get the previous step based on the current step.
     * If the current step is not provided, it uses the currentStep property.
     * If there is no previous step, it returns null.
     *
     * @param  string|Step|null  $currentStep  A step identifier or null to use `$this->currentStep`.
     * @param  bool   $accessible   If true, ensures the previous step is accessible.
     * @return Step|null
     */
    public function getPreviousStep(mixed $currentStep = null, bool $accessible = true): ?Step
    {
        $currentStep = $currentStep ?? $this->currentStep;
        $currentIndex = $this->getStepIndex($currentStep);
        if ($currentIndex === null || $currentIndex <= 0) {
            return null; // No previous step or current step is the first one
        }
        $previousIndex = $currentIndex - 1;

        return (! $accessible || $this->isAccessible($previousIndex)) ? $this->getStep($previousIndex) : null;
    }

    /**
     * Return the next step based on the current step.
     * If the current step is not provided, it uses the currentStep property.
     * If there is no next step, it returns null.
     *
     * @param  string|Step|null  $currentStep  A step identifier or null to use `$this->currentStep`.
     * @param  bool   $accessible   If true, ensures the next step is accessible.
     * @return Step|null
     */
    public function getNextStep(mixed $currentStep = null, bool $accessible = true): ?Step
    {
        $currentStep = $currentStep ?? $this->currentStep;
        $currentIndex = $this->getStepIndex($currentStep);

        if ($currentIndex === null || $currentIndex >= count($this->allSteps) - 1) {
            return null; // No next step or current step is the last one
        }
        $nextIndex = $currentIndex + 1;

        return (! $accessible || $this->isAccessible($nextIndex)) ? $this->getStep($nextIndex) : null;
    }

    /**
     * Get the last discovered step.
     *
     * When `$accessible` is true the last step will be chosen from the
     * subset of steps that are visible and enabled.
     *
     * @param  bool  $accessible
     * @return Step|null
     */
    public function getLastStep(bool $accessible = false): ?Step
    {
        $allSteps = $this->getAllSteps($accessible);
        if (empty($allSteps)) {
            return null; // No steps available
        }
        $lastStepClass = array_key_last($allSteps);

        return $this->getStep($lastStepClass);
    }

    /**
     * Return the first discovered step instance, or null when none exist.
     *
     * @return Step|null
     */
    public function getFirstStep(): ?Step
    {
        $allSteps = $this->allSteps;
        if (empty($allSteps)) {
            return null; // No steps available
        }
        $firstStepClass = array_key_first($allSteps);

        return $this->getStep($firstStepClass);
    }

    /**
     * Return the 0-based index of a step given its class name or `Step` instance.
     *
     * @param  Step|string  $step  Step instance or class name.
     * @return int|null  0-based index or null when not found.
     */
    public function getStepIndex(Step|string $step): ?int
    {
        $stepClass = $step instanceof Step ? $step::class : $step;
        $index = array_search($stepClass, array_keys($this->allSteps));

        return $index !== false ? $index : null; // Return -1 if not found
    }

    /**
     * Determine whether a step is enabled.
     *
     * If the step object implements an `enabled()` method it will be called.
     * Otherwise the step is considered enabled by default.
     *
     * @param  Step|string|StepDetails|int  $step
     * @return bool
     */
    public function isEnabled(Step|string|StepDetails|int $step): bool
    {
        $step = $this->getStep($step);

        if (method_exists($step, 'enabled')) {
            return (bool) $step->enabled();
        }

        return true; // Default to true if not found or no enabled method
    }

    /**
     * Determine whether a step is visible.
     *
     * If the step object implements a `visible()` method it will be called.
     * Otherwise the step is considered visible by default.
     *
     * @param  Step|string|StepDetails|int  $step
     * @return bool
     */
    public function isVisible(Step|string|StepDetails|int $step): bool
    {
        $step = $this->getStep($step);

        if (method_exists($step, 'visible')) {
            return (bool) $step->visible();
        }

        return true; // Default to false if not found or no visible method
    }

    /**
     * Check whether the specified step is valid.
     *
     * If the step implements an `isValid()` method it will be used, otherwise
     * the step is considered valid by default.
     *
     * @param  Step|string|StepDetails|int  $step
     * @return bool
     */
    public function isValid(Step|string|StepDetails|int $step): bool
    {
        $step = $this->getStep($step);

        if (method_exists($step, 'isValid')) {
            return (bool) $step->isValid();
        }

        // Default to true if no isValid method exists
        return true;
    }

    /**
     * Return a rendered view instance for the given step when available.
     *
     * If the step class implements `render()` the returned view will be used.
     *
     * @param  Step|string|StepDetails|int  $step
     * @return \Illuminate\Contracts\View\View|null
     */
    public function getView(Step|string|StepDetails|int $step): ?\Illuminate\Contracts\View\View
    {
        $step = $this->getStep($step);

        if (method_exists($step, 'render')) {
            return $step->render();
        }

        return null;
        // todo:
        // determine the view based on the step class name
        // $stepClass = is_string($step) ? $step : get_class($step);
        // $viewName = str_replace('\\', '.', $stepClass);
        // return "livewire.{$viewName}";
    }

    /**
     * Return the `StepDetails` DTO for the given step identifier.
     *
     * @param  Step|string|int|StepDetails  $step
     * @return StepDetails|null
     */
    public function getStepDetails(Step|string|int|StepDetails $step): ?StepDetails
    {
        if ($step instanceof StepDetails) {
            return $step;
        }
        $name = $this->getStepName($step);

        return $this->steps->where('name', $name)->first();
    }

    /**
     * Determine whether the given step is the last step in the wizard.
     * is the step the last (accessible) step?
     * This checks if the step is the last step in the allSteps array.
     * It does not check if the step is accessible or visible.
     * If $accessible is true, it will check if it is the last visible and enabled step.
     *
     * @param  Step|string|StepDetails|int|null  $step  Step identifier or null to use `$this->currentStep`.
     * @param  bool   $accessible  If true the last accessible step is considered.
     * @return bool
     */
    public function isLastStep(mixed $step = null, bool $accessible = false): bool
    {
        $step = $this->getStep($step ?? $this->currentStep);
        if (! $step) {
            return false; // Step not found
        }

        // Get the last step in the allSteps array
        $lastStep = $this->getLastStep($accessible);
        if (! $lastStep) {
            return false; // No last step found
        }

        // Compare the current step with the last step
        return $step::class === $lastStep::class;
    }

    /**
     * Move to the next step and optionally dispatch a `stepChanged` event.
     *
     * @param  bool  $dispatch  Whether to dispatch the `stepChanged` event.
     * @return Step|null  The step moved to, or null when no next step exists.
     */
    public function gotoNextStep(bool $dispatch = false): ?Step
    {
        $currentStep = $this->getStep($this->currentStep);
        if (! $currentStep) {
            return null; // No current step found
        }

        $nextStep = $this->getNextStep($currentStep);

        return $nextStep ? $this->gotoStep($nextStep, $dispatch) : null;
    }

    /**
     * Move to the previous step and optionally dispatch a `stepChanged` event.
     *
     * @param  bool  $dispatch
     * @return Step|null
     */
    public function gotoPreviousStep(bool $dispatch = false): ?Step
    {
        $currentStep = $this->getStep($this->currentStep);
        if (! $currentStep) {
            return null; // No current step found
        }

        $previousStep = $this->getPreviousStep($currentStep);

        return $previousStep ? $this->gotoStep($previousStep, $dispatch) : null;
    }

    /**
     * Navigate to a specific step by identifier or instance.
     *
     * If the step is not found or not accessible it will return null.
     *
     * @param  Step|int|string|StepDetails  $step
     * @param  bool  $dispatch  Whether to dispatch the `stepChanged` event.
     * @return Step|null
     */
    public function gotoStep(Step|int|string|StepDetails $step, bool $dispatch = false): ?Step
    {
        $step = $this->getStep($step);
        if (! $step || ! $this->isEnabled($step) || ! $this->isVisible($step)) {
            return null; // Step not found or not accessible
        }

        // Update current step to the specified step
        $this->currentStep = $this->getStepName($step);
        if ($dispatch) {
            $this->dispatch('stepChanged', ['step' => $step]);
        }

        return $step;
    }

    /**
     * Navigate to the first step if accessible.
     *
     * @param  bool  $dispatch
     * @return Step|null
     */
    public function gotoFirstStep(bool $dispatch = false): ?Step
    {
        $firstStep = $this->getFirstStep();
        if (! $firstStep || ! $this->isEnabled($firstStep) || ! $this->isVisible($firstStep)) {
            return null; // No first step or not accessible
        }

        return $this->gotoStep($firstStep, $dispatch);
    }

    /**
     * Navigate to the last step. When `$accessible` is true the last accessible
     * step will be used.
     *
     * @param  bool  $dispatch
     * @param  bool  $accessible
     * @return Step|null
     */
    public function gotoLastStep(bool $dispatch = false, bool $accessible = true): ?Step
    {
        $lastStep = $this->getLastStep($accessible);
        if (! $lastStep || ! $this->isEnabled($lastStep) || ! $this->isVisible($lastStep)) {
            return null; // No last step or not accessible
        }

        return $this->gotoStep($lastStep, $dispatch);
    }

    /**
     * Find the first invalid step and navigate to it.
     *
     * @return Step|null
     */
    public function gotoFirstInvalidStep(): ?Step
    {
        // Iterate through all steps to find the first invalid step
        foreach ($this->steps as $step) {
            if (! $this->isValid($step)) {
                return $this->gotoStep($step);
            }
        }

        return null; // All steps are valid
    }

    /**
     * Determine whether there is a visible next step relative to the given/current step.
     *
     * @param  mixed  $step  Step identifier or null to use `$this->currentStep`.
     * @param  bool   $accessible  When true, only consider accessible steps.
     * @return bool
     */
    public function hasNextStep(mixed $step = null, bool $accessible = true): bool
    {
        $step = $step ?? $this->currentStep;
        $currentStep = $this->getStep($step);
        if (! $currentStep) {
            return false; // No current step found
        }

        $nextStep = $this->getNextStep($currentStep);

        return $nextStep && $this->isVisible($nextStep); // && $this->isEnabled($nextStep)
    }

    /**
     * Determine whether there is a visible previous step relative to the given/current step.
     *
     * @param  mixed  $step  Step identifier or null to use `$this->currentStep`.
     * @return bool
     */
    public function hasPreviousStep(mixed $step = null): bool
    {
        $step = $step ?? $this->currentStep;
        $currentStep = $this->getStep($step);
        if (! $currentStep) {
            return false; // No current step found
        }

        $previousStep = $this->getPreviousStep($currentStep);

        return $previousStep && $this->isVisible($previousStep); // && $this->isEnabled($previousStep)
    }

    public function isAllValid(): bool
    {
        // Check if all steps are valid
        return $this->steps->every(function ($step) {
            return $this->isValid($step);
        });
    }

    /**
     * Return a collection of `StepDetails` DTOs representing the discovered steps.
     *
     * The collection is indexed numerically (0-based) and contains
     * `\Ympact\Wizard\Livewire\SupportWizardObjects\Values\StepDetails` instances describing title, icon,
     * description and marker information for each step.
     *
     * @return \Illuminate\Support\Collection<int, \Ympact\Wizard\Livewire\SupportWizardObjects\Values\StepDetails>
     */
    #[Computed]
    public function steps(): \Illuminate\Support\Collection
    {
        // get title, description, icon, initial state, etc. of the steps
        return collect($this->getStepObjects())->map(function ($name, $stepClass) {
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
        })->values();
    }

    /**
     * Discover public properties on the component whose types extend `Step`.
     *
     * Returns an associative array mapping step class FQCN => property name.
     * @todo replace HandlesStepObjects with this code
     * @return array<string,string>
    */
    #[Computed]
    public function allSteps(): array
    {
        $steps = [];
        $reflection = new ReflectionClass($this);

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $type = $property->getType();
            $className = $type ? (string) $type : null;
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
     * Return an array of step class FQCN => property name, optionally filtered.
     *
     * When filtering flags are provided the returned array will only include
     * steps that satisfy the requested conditions.
     *
     * @param  bool  $accessible  If true, implies `$visible = $enabled = true`.
     * @param  bool  $enabled
     * @param  bool  $visible
     * @param  bool  $valid
     * @return array<string,string>
     */
    public function getAllSteps(bool $accessible = false, bool $enabled = false, bool $visible = false, bool $valid = false): array
    {
        $allSteps = $this->allSteps;

        if ($accessible) {
            $visible = true;
            $enabled = true;
        }

        if ($enabled || $visible || $valid) {
            // filter out the steps that are not enabled or visible
            $allSteps = array_filter($allSteps, function ($stepClass) use ($enabled, $visible, $valid) {
                $step = $this->getStep($stepClass);

                return (! $enabled || $this->isEnabled($step))
                    && (! $visible || $this->isVisible($step))
                    && (! $valid || $this->isValid($step));
            });
        }

        return $allSteps;
    }

}