<?php

namespace Ympact\Wizard\Livewire\SupportWizardObjects;

/**
 * Helpers for working with discovered Wizard objects.
 */
trait HandlesWizardObjects
{
    /**
     * Return an indexed array of discovered `Wizard` instances on the component.
     *
     * @return array<int, Wizard>
     */
    public function getWizardObjects(): array
    {
        $wizards = [];

        foreach (get_object_vars($this) as $value) {
            if ($value instanceof Wizard) {
                $wizards[] = $value;
            }
        }

        return $wizards;
    }
}
