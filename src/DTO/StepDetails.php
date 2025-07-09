<?php

namespace Ymapct\Wizard\DTO;


class StepDetails
{
    public string $key;
     public string $name;
    public string $class;
    public int $index;
    public ?string $title = null;
    public ?string $description = null;
    public ?string $icon = null;
    
    public function __construct(
        string $key = '',
        string $name = '',
        string $class = '',
        int $index = 0,
        ?string $title = null,
        ?string $description = null,
        ?string $icon = null
    ) {
        $this->key = $key;
        $this->name = $name;
        $this->class = $class;
        $this->index = $index;
        $this->title = $title;
        $this->description = $description;
        $this->icon = $icon;
    }
}