<?php

namespace App\Internal\About\DTO;

class AboutDTO
{

    #declare property store core_values,experience_during,description,project_is_done,client_response,img
    public array $core_values;
    public int $experience_during;
    public string $description;
    public int $project_is_done;
    public int $client_response;
    public ?string $img;

    #recive property of request core_values,experience_during,description,project_is_done,client_response,img(base 64)
    public function __construct(
        array $core_values,
        int $experience_during,
        string $description,
        int $project_is_done,
        int $client_response,
        ?string $img
    ) {
        $this->core_values = $core_values;
        $this->experience_during = $experience_during;
        $this->description = $description;
        $this->project_is_done = $project_is_done;
        $this->client_response = $client_response;
        $this->img = $img;
    }
}
