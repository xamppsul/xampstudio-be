<?php

namespace App\Domain\Slider\Entities;

class SliderDomainEntities
{
    #declare property for store data
    private int $id;
    private string $img;
    private string $title;
    private string $description;
    private string $position;

    #inject data: id,img,title,description,position & token(as session)
    public function __construct(int $id, string $img, string $title, string $description, int $position)
    {
        $this->id = $id;
        $this->img = $img;
        $this->title = $title;
        $this->description = $description;
        $this->position = $position;
    }

    #declare method for return data as type
    public function GetID(): ?int
    {
        return $this->id ?? null;
    }

    public function GetImg(): ?string
    {
        return $this->img ?? null;
    }

    public function GetTitle(): ?string
    {
        return $this->title ?? null;
    }

    public function GetDescription(): ?string
    {
        return $this->description ?? null;
    }

    public function GetPosition(): ?int
    {
        return $this->position ?? null;
    }
}
