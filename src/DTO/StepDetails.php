<?php

namespace Ympact\Wizard\DTO;


class StepDetails
{
    public int $index;
    public string $name;
    public string $class;
    public string $marker = null;
    public ?string $title = null;
    public ?string $description = null;
    public ?string $icon = null;
    
    public function __construct(
        int $index = 0,
        string $name = '',
        string $class = '',
        string $marker = '1',
        ?string $title = null,
        ?string $description = null,
        ?string $icon = null
    ) {
        $this->index = $index;
        $this->name = $name;
        $this->class = $class;
        $this->marker = $marker;
        $this->title = $title;
        $this->description = $description;
        $this->icon = $icon;
    }
}