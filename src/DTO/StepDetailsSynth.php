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
            'key' => $target->key,
            'name' => $target->key,
            'class' => $target->class,
            'index' => $target->index,
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
 
        $instance->key = $value['key'] ?? '';
        $instance->class = $value['name'] ?? '';
        $instance->class = $value['class'] ?? '';
        $instance->index = $value['index'] ?? 0;
        $instance->title = $value['title'] ?? null;
        $instance->description = $value['description'] ?? null;
        $instance->icon = $value['icon'] ?? null;
        //$instance->enabled = $value['enabled'] ?? true;
        //$instance->visible = $value['visible'] ?? true;

 
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