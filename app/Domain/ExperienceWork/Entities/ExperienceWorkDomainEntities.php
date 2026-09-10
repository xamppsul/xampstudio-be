<?php

namespace App\Domain\ExperienceWork\Entities;

class ExperienceWorkDomainEntities
{
    #declare property for store data
    private int             $id;
    private string          $title;
    private string          $start_at;
    private string|null     $end_at;
    private string          $position;
    private string          $description;
    private array           $achivement;
    private array           $tech;

    #inject data: id,title,start_at,end_at,position,description,achivement,tech
    public function __construct(
        int                 $id,
        string              $title,
        string              $start_at,
        string|null         $end_at,
        string              $position,
        string              $description,
        array               $achivement = [],
        array               $tech = [],
    ) {
        $this->id           = $id;
        $this->title        = $title;
        $this->start_at     = $start_at;
        $this->end_at       = $end_at;
        $this->position     = $position;
        $this->description  = $description;
        $this->achivement   = $achivement;
        $this->tech         = $tech;
    }

    #declare method for return data as type
    public function GetID(): ?int
    {
        return $this->id ?? 0;
    }

    public function GetTitle(): ?string
    {
        return $this->title ?? 'tidak ada title';
    }

    public function GetStartAt(): ?string
    {
        return $this->start_at ?? '0000-00-00';
    }

    public function GetEndAt(): ?string
    {
        return $this->end_at ?? 'masih sementara bekerja';
    }

    public function GetPosition(): ?string
    {
        return $this->position ?? 'tidak ada position';
    }

    public function GetDescription(): ?string
    {
        return $this->description ?? 'tidak ada description';
    }

    public function GetAchivement(): ?array
    {
        return $this->achivement ?? [];
    }

    public function GetTech(): ?array
    {
        return $this->tech ?? [];
    }
}
