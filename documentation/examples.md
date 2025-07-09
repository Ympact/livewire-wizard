# Example scripts

## Flux implementation steps

```blade
<flux:step.group>
    <flux:steps>
    @foreach($this->steps as $step)
        @if($this->isVisible($step))
        <flux:step :name="$step->key" :icon="$step->icon" :disabled="!$this->isEnabled($step)">
            {{ $step->title }} 
            @if($this->isValid($step))
                <flux:icon color="green" size="sm" variant="outline" icon="check-circle"/>
            @endif
        </flux:step>
        @endif
    @endforeach
    </flux:steps>
    @foreach($this->steps as $step)
        <flux:step.panel :name="$step->key">
            
            {{ $this->getView($step) }}
        </flux:step.panel>
    @endforeach
</flux:step.group>
```