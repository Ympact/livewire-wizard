<?php

namespace Ymapct\Wizard\Livewire\SupportStepObjects;

trait HandlesStepObjects
{
    public function getStepObjects()
    {
        $steps = [];

        foreach ($this->all() as $key => $value) {
            if ($value instanceof Step) {
                $steps[] = $value;
            }
        }

        return $steps;
    }
}
