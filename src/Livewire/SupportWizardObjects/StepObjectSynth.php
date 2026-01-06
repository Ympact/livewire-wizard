<?php

namespace Ympact\Wizard\Livewire\SupportWizardObjects;

use Livewire\Drawer\Utils;
use Livewire\Features\SupportAttributes\AttributeCollection;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

use function Livewire\wrap;

class StepObjectSynth extends Synth
{
    public static string $key = 'step';

    public static function match(mixed $target): bool
    {
        return $target instanceof Step;
    }

    /**
     * @return array{0: array<string,mixed>,1: array<string,mixed>}
     */
    public function dehydrate(mixed $target, callable $dehydrateChild): array
    {
        $data = $target->toArray();

        foreach ($data as $key => $child) {
            $data[$key] = $dehydrateChild($key, $child);
        }

        return [$data, ['class' => get_class($target)]];
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,mixed> $meta
     */
    public function hydrate(array $data, array $meta, callable $hydrateChild): object
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

    public function set(mixed &$target, mixed $key, mixed $value): void
    {
        if ($value === null && Utils::propertyIsTyped($target, $key) && ! Utils::getProperty($target, $key)->getType()->allowsNull()) {
            unset($target->$key);
        } else {
            $target->$key = $value;
        }
    }

    public static function bootStepObject(mixed $component, mixed $step, string $path): callable
    {
        $component->mergeOutsideAttributes(
            AttributeCollection::fromComponent($component, $step, $path.'.')
        );

        return function () use ($step) {
            wrap($step)->boot();
        };
    }
}
