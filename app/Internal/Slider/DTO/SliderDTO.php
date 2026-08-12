<?php

namespace App\Internal\Slider\DTO;

class SliderDTO
{

    #declare property store img,title,description,position,status
    public string $img, $title, $description;
    public int $position;
    public bool $status;

    #recive property of request img(base64),title(string),description(string),position(string),status(bool)
    public function __construct(
        string $img,
        string $title,
        string $description,
        int $position,
        bool $status = false
    ) {
        $this->img = $img;
        $this->title = $title;
        $this->description = $description;
        $this->position = $position;
        $this->status = $status;
    }
}
