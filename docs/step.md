# Step component

The step component defines an individual wizard step. In your blade you define whether this step is displayed as a tab, a card, a fieldset or the next modal.

There are various methods and properties that help [define the logic and properties](#defining-your-step-component) of a step. Next to that the step component has [supporting methods](#supporting-methods) that crate easy access to the wizard component.

## Step validation

The Step component extends Livewire's Form component. We make use of the form validation functionality to determine whether the input given in a Step is valid and hence the entire step is valid. This is done real time when there are rules defined in your step. In case you do not want to automatically validate a step, you can always insert the `isValid()` method in your Step component and make it return `true`. This will prevent the validation rules from being executed real-time.

## Defining your Step component

You can make use of the following properties and methods to define your step component and its logic:

> [!NOTE]  
> The Step component does not support Livewires Attributes such as Computed properties.

### Properties

#### `public $stepTitle`

#### `public $stepDescription`

#### `public $stepIcon`

### Logic methods

#### `enabled(): ?bool`

#### `isValid(): ?bool`

#### `render(): View`

\Illuminate\Contracts\View\View

#### `rules()`

#### `visible(): ?bool`

#### `summary(): string|View|null`

## Supporting methods

Below are the supporting methods that the wizard package provides out of the box.

### Available

#### `getStep()`

#### `getLastStep()`

#### `getFirstStep()`

#### `getOwnIndex()`

#### `previousStep()`

#### `nextStep()`

### Accessing other methods or properties

In case you want to access other methods or properties from your Step component on the Wizard component you can use `$this->getComponent()`:

```php
function lastStep(){
    return $this->getComponent()->getLastStep();
}
```
