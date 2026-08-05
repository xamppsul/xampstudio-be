<?php

namespace App\Internal\Slider\DTO;

class SliderDTO
{

    #declare property store email & password
    public string $img, $title, $description;
    public int $position;

    #receive data email & password
    public function __construct(
        string $img,
        string $title,
        string $description,
        int $position
    ) {
        $this->img = $img;
        $this->title = $title;
        $this->description = $description;
        $this->position = $position;
    }
}
