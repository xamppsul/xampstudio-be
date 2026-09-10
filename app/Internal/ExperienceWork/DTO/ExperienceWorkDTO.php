<?php

namespace App\Internal\ExperienceWork\DTO;

class ExperienceWorkDTO
{

    #declare property store core_values,experience_during,description,project_is_done,client_response,img
    public string $title;
    public string $start_at;
    public string|null $end_at;
    public string $position;
    public string $description;
    public array $achivement;
    public array $tech;

    #recive property of request core_values,experience_during,description,project_is_done,client_response,img(base 64)
    public function __construct(
        string $title,
        string $start_at,
        string|null $end_at,
        string $position,
        string $description,
        array $achivement = [],
        array $tech = []
    ) {
        $this->title = $title;
        $this->start_at = $start_at;
        $this->end_at = $end_at;
        $this->position = $position;
        $this->description = $description;
        $this->achivement = $achivement;
        $this->tech = $tech;
    }
}
