# Wizard component

The Wizard component provides various methods for use in the component, blade view or by the Step components.

## Defining your Wizard component

### Steps

Steps are defined by listing them as public properties typed with their Step class name. The order in which they are listed is the order in which they will be indexed and (depending on your blade implementation) rendered.

```php
namespace App\Livewire\CheckoutForm;

use App\Livewire\MyForm\Steps\MembersStep;
use App\Livewire\MyForm\Steps\SubscriptionStep;
use App\Livewire\MyForm\Steps\TenantStep;
use Ympact\Wizard\Wizard;

class Index extends Wizard
{
    // Steps for the checkout form
    public ProductStep $products;
    public ContactStep $contact;
    public PaymentStep $payment;
}
```

Many of the methods that require a `$step` as parameter accept either the property name, class name or the index.

```php
    public ProductStep $products;
    public ContactStep $contact;
    public PaymentStep $payment;

    // class name
    $this->hasNextStep(ContactStep::class) // true

    // property name
    $this->hasNextStep('payment') // false

    // index
    $this->hasNextStep(2) // false
```

### View

//

### Definition methods

The following methods can be used to help define your steps.

#### `stepDetails()`

#### `stepMarker()`

#### `mount() : void`

There is already a `mount()` method defined in the parent wizard component. It will set the first defined step in your wizard component as the `$currentStep`. You may pass a `$currentStep` to your component to set a different initial step. 

In case you need to define your own mount method, you may add `parent::mount()` to keep this functionality working.

### Properties

#### `public $currentStep`

### Supporting methods

#### `getStep($ste): ?Step`

#### `isAccessible($ste): bool`

#### `getStepName($ste): ?string`

#### `getPreviousStep($currentStep = null, $accessible = true): ?Step`

#### `getNextStep($currentStep = null, $accessible = true): ?Step`

#### `getLastStep($accessible = false): ?Step`

#### `getFirstStep(): ?Step`

#### `getStepIndex(Step|string $step): ?int`

#### `isEnabled($step): bool`

#### `isVisible($step): bool`

#### `isValid($step): bool`

#### `getView($step)`

#### `getStepDetails($step): ?StepDetails`

#### `isLastStep($step = null, $accessible = false): bool`

#### `gotoNextStep($dispatch = false): ?Step`

#### `gotoPreviousStep($dispatch = false): ?Step`

#### `gotoStep($step, $dispatch = false): ?Step`

#### `gotoFirstStep($dispatch = false): ?Step`

#### `gotoLastStep($dispatch = false, $accessible = true): ?Step`

#### `gotoFirstInvalidStep(): ?Step`

#### `hasNextStep($accessible = true) : bool`

#### `hasPreviousStep(): bool`

#### `isAllValid(): bool`

#### `getAllSteps($accessible = false, $enabled = false, $visible = false, $valid = false): array`

### Computed properties

#### `steps`

#### `allSteps`
