<?php

use Tests\TestCase;
use Ympact\Wizard\Livewire\SupportWizardObjects\Wizard;
use Ympact\Wizard\Livewire\SupportWizardObjects\Step;
use Ympact\Wizard\Livewire\SupportWizardObjects\Values\StepDetails;
use Ympact\Wizard\Livewire\SupportWizardObjects\Values\StepDetailsSynth;
use Livewire\Component;

class SupportWizardObjectsTest extends TestCase
{
    public function test_wizard_get_step_by_various_identifiers()
    {
        $component = new class extends Component {};

        $wizard = new Wizard($component, 'wizard');

        // prepare a dummy Step instance by overriding the constructor to avoid Livewire Form init
        $step = new class extends Step {
            public function __construct() {}
        };

        // configure wizard steps mapping: className => propertyName
        $classKey = 'App\\Steps\\DummyStep';
        $wizard->steps = [ $classKey => 'myStepProp' ];

        // set the property on the wizard instance as the synth would
        $wizard->myStepProp = $step;

        // by index
        $this->assertSame($step, $wizard->getStep(0));

        // by property name
        $this->assertSame($step, $wizard->getStep('myStepProp'));

        // by class key
        $this->assertSame($step, $wizard->getStep($classKey));

        // by StepDetails
        $details = new StepDetails(index: 0, name: 'myStepProp', class: $classKey, marker: '1');
        $this->assertSame($step, $wizard->getStep($details));

        // by Step instance directly
        $this->assertSame($step, $wizard->getStep($step));
    }

    public function test_step_details_synth_hydrate_and_dehydrate()
    {
        $synth = new class extends StepDetailsSynth {
            public function __construct() {}
        };

        $details = new StepDetails(index: 2, name: 'foo', class: 'App\\Steps\\Foo', marker: '3', title: 'T', description: 'D', icon: 'I');

        [$dehydrated, $meta] = $synth->dehydrate($details);

        $this->assertSame(2, $dehydrated['index']);
        $this->assertSame('foo', $dehydrated['name']);
        $this->assertSame('App\\Steps\\Foo', $dehydrated['class']);

        $hydrated = $synth->hydrate($dehydrated);

        $this->assertInstanceOf(StepDetails::class, $hydrated);
        $this->assertSame(2, $hydrated->index);
        // Note: hydrate implementation maps 'name' to class then overwrites with 'class'
        $this->assertSame('App\\Steps\\Foo', $hydrated->class);
        $this->assertSame('3', (string) $hydrated->marker);
    }
}
