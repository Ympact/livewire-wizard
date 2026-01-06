# Livewire Wizard

## Version 1

- Two implemenations possible:
  1. extended: through Wizard Object
     - A livewire component defines a wizard object
     - The wizard object defines its steps
  2. simple: define the steps on a livewire object
     - the livewire component should defined one or multiple Step Objects and preferably use the WizardTrait
     - WizardComponent is a simple implementation that could be used as base component
- support livewire v4
  - singlefile componentsmultifile and multifile components
  - colocation of wizard and step classes with the components that resides in /resources/views/*
  - support new livewire v4 features
- provide default view: simple form stepper (horizontal layout)
- support dynamic steps (specific step type that provides multiple steps based on certain data input)
  - dynamic conditions and next steps
  - simple database input
  - support network/process for dynamic steps (allow recursivity )
- output process schema to draw the proces (including branches)

## Future verions

- provide more views
  - partials
    - progress bar
    - process schema
    - history/navigation of current step
  - variants
    - cards popuping up
    - transitioning slides
