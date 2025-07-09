# Quick start

The wizard an Wizard Component (an extended Livewire component) in combination with Step components (extended Livewire Form components).
That means that they the functionality of those base components can be used within your Wizard or Step classes.


## Quick example

### The Wizard component

For the wizard you create a `Wizard` component.
It holds the Step components as public properties. The order of the steps is the order in which they will be rendered.
The wizard component provides many [more methods](wizard.md) for defining the wizard and adding functionality.

```php
namespace App\Livewire\CheckoutForm;

use App\Livewire\MyForm\Steps\MembersStep;
use App\Livewire\MyForm\Steps\SubscriptionStep;
use App\Livewire\MyForm\Steps\TenantStep;
use Ympact\Wizard\Wizard;

class Index extends Wizard
{
    public ProductStep $products;
    public ContactStep $contact;
    public PaymentStep $payment;

    public function render(){
        return view('livewire.check-out.index');
    }
}
```

### A Step component

For each step you create `Step` component. It can define logic to determine whether the step is visible and enabled. 


```php
namespace App\Livewire\CheckoutForm\Steps;

use Ympact\Wizard\Step;

class ContactStep extends Step
{
    public $name;

    public $address;

    protected function rules(): array
    {        
        return [
            'name' => 'required|string',
            'address' => 'required|string', 
        ];
    }

    public function render(){
        return view('livewire.check-out.steps.products');
    }
}

```

