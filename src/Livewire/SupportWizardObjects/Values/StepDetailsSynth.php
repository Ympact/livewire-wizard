<?php

namespace Ympact\Wizard\Livewire\SupportWizardObjects\Values;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class StepDetailsSynth extends Synth
{
    public static string $key = 'step-details';

    public static function match(mixed $target): bool
    {
        return $target instanceof StepDetails;
    }

    /**
     * @return array{0: array<string,mixed>,1: array<string,mixed>}
     */
    public function dehydrate(mixed $target): array
    {
        return [[
            'index' => $target->index,
            'name' => $target->name,
            'class' => $target->class,
            'marker' => $target->marker,
            'title' => $target->title,
            'description' => $target->description,
            'icon' => $target->icon,
            // 'enabled' => $target->enabled,
            // 'visible' => $target->visible,
        ], []];
    }

    public function hydrate(mixed $value): StepDetails
    {
        $instance = new StepDetails();

        $instance->index = $value['index'] ?? 0;
        $instance->class = $value['name'] ?? '';
        $instance->class = $value['class'] ?? '';
        $instance->marker = $value['marker'] ?? 1;
        $instance->title = $value['title'] ?? null;
        $instance->description = $value['description'] ?? null;
        $instance->icon = $value['icon'] ?? null;

        return $instance;
    }

    public function get(mixed &$target, mixed $key): mixed
    {
        return $target->{$key};
    }

    public function set(mixed &$target, mixed $key, mixed $value): void
    {
        $target->{$key} = $value;
    }
}
