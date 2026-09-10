<?php

namespace App\Domain\About\Entities;

class AboutDomainEntities
{
    #declare property for store data
    private int     $id;
    private array   $core_values;
    private int     $experience_during;
    private string  $description;
    private int     $project_is_done;
    private int     $client_response;
    private string  $img;
    private bool    $status;

    #inject data: id,core_values,experience_during,description,project_is_done,client_response,img & status
    public function __construct(
        int        $id,
        array      $core_values,
        int        $experience_during,
        string     $description,
        int        $project_is_done,
        int        $client_response,
        string     $img,
        bool       $status
    ) {
        $this->id                   = $id;
        $this->core_values          = $core_values;
        $this->experience_during    = $experience_during;
        $this->description          = $description;
        $this->project_is_done      = $project_is_done;
        $this->client_response      = $client_response;
        $this->img                  = $img;
        $this->status               = $status;
    }

    #declare method for return data as type
    public function GetID(): ?int
    {
        return $this->id ?? 0;
    }

    public function GetCoreValuesID(): ?array
    {
        return $this->core_values ?? [];
    }

    public function GetExperienceDuring(): ?int
    {
        return $this->experience_during ?? 0;
    }

    public function GetDescription(): ?string
    {
        return $this->description ?? null;
    }

    public function GetProjectIsDone(): ?int
    {
        return $this->project_is_done ?? 0;
    }

    public function GetClientResponse(): int
    {
        return $this->client_response ?? 0;
    }

    public function GetImg(): ?string
    {
        return $this->img ?? null;
    }

    public function GetStatus(): ?bool
    {
        return $this->status ?? false;
    }
}
