<?php

namespace Ympact\Wizard\Livewire\SupportWizardObjects;

use Livewire\Drawer\Utils;
use Livewire\Features\SupportAttributes\AttributeCollection;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

use function Livewire\wrap;

class WizardObjectSynth extends Synth
{
    public static string $key = 'wizard';

    public static function match(mixed $target): bool
    {
        return $target instanceof Wizard;
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
        $wizard = new $meta['class']($this->context->component, $this->path);

        $callBootMethod = static::bootWizardObject($this->context->component, $wizard, $this->path);

        foreach ($data as $key => $child) {
            if ($child === null && Utils::propertyIsTypedAndUninitialized($wizard, $key)) {
                continue;
            }

            $wizard->$key = $hydrateChild($key, $child);
        }

        $callBootMethod();

        return $wizard;
    }

    public function set(mixed &$target, mixed $key, mixed $value): void
    {
        if ($value === null && Utils::propertyIsTyped($target, $key) && ! Utils::getProperty($target, $key)->getType()->allowsNull()) {
            unset($target->$key);
        } else {
            $target->$key = $value;
        }
    }

    public static function bootWizardObject(mixed $component, mixed $wizard, string $path): callable
    {
        $component->mergeOutsideAttributes(
            AttributeCollection::fromComponent($component, $wizard, $path.'.')
        );

        return function () use ($wizard) {
            wrap($wizard)->boot();
        };
    }
}
