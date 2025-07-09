<?php

namespace Ymapct\Wizard\DTO;

use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use Ymapct\Wizard\DTO\StepDetails;

class StepDetailsSynth extends Synth
{
    public static $key = 'step-details';
 
    public static function match($target)
    {
        return $target instanceof StepDetails;
    }
    public function dehydrate($target)
    {
        return [[
            'index' => $target->index,
            'name' => $target->name,
            'class' => $target->class,
            'marker' => $target->marker,
            'title' => $target->title,
            'description' => $target->description,
            'icon' => $target->icon,
            //'enabled' => $target->enabled,
            //'visible' => $target->visible,
        ], []];
    }
 
    public function hydrate($value)
    {
        $instance = new StepDetails;
 
        $instance->index = $value['index'] ?? 0;
        $instance->class = $value['name'] ?? '';
        $instance->class = $value['class'] ?? '';
        $instance->marker = $value['marker'] ?? 1;
        $instance->title = $value['title'] ?? null;
        $instance->description = $value['description'] ?? null;
        $instance->icon = $value['icon'] ?? null;
 
        return $instance;
    }
 
    public function get(&$target, $key) 
    {
        return $target->{$key};
    }
 
    public function set(&$target, $key, $value)
    {
        $target->{$key} = $value;
    }
}