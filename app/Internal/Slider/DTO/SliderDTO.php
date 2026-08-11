<?php

namespace App\Internal\Slider\DTO;

class SliderDTO
{

    #declare property store email & password
    public string $title, $description;
    public int $position;
    public bool $status;

    #receive data email & password
    public function __construct(
        string $title,
        string $description,
        int $position,
        bool $status = false
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->position = $position;
        $this->status = $status;
    }
}
