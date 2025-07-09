<?php

namespace Ymapct\Wizard\Livewire\SupportStepObjects;

use Livewire\Drawer\Utils;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use Livewire\Features\SupportAttributes\AttributeCollection;

use function Livewire\wrap;

class StepObjectSynth extends Synth {
    public static $key = 'step';

    static function match($target)
    {
        return $target instanceof Step;
    }

    function dehydrate($target, $dehydrateChild)
    {
        $data = $target->toArray();

        foreach ($data as $key => $child) {
            $data[$key] = $dehydrateChild($key, $child);
        }

        return [$data, ['class' => get_class($target)]];
    }

    function hydrate($data, $meta, $hydrateChild)
    {
        $step = new $meta['class']($this->context->component, $this->path);

        $callBootMethod = static::bootStepObject($this->context->component, $step, $this->path);

        foreach ($data as $key => $child) {
            if ($child === null && Utils::propertyIsTypedAndUninitialized($step, $key)) {
                continue;
            }

            $step->$key = $hydrateChild($key, $child);
        }

        $callBootMethod();

        return $step;
    }

    function set(&$target, $key, $value)
    {
        if ($value === null && Utils::propertyIsTyped($target, $key) && ! Utils::getProperty($target, $key)->getType()->allowsNull()) {
            unset($target->$key);
        } else {
            $target->$key = $value;
        }
    }

    public static function bootStepObject($component, $step, $path)
    {
        $component->mergeOutsideAttributes(
            AttributeCollection::fromComponent($component, $step, $path . '.')
        );

        return function () use ($step) {
            wrap($step)->boot();
        };
    }
}

