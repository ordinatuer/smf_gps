<?php

namespace App\Entity;

use App\Repository\PointsRepository;
use App\geo\Objects\Point;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PointsRepository::class)]
#[ORM\Table(name:'points_list')]
class Points
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'point')]
    private $location;

    #[ORM\Column(type: 'float')]
    private $location_latitude;

    #[ORM\Column(type: 'float')]
    private $location_longitude;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): ?Point
    {
        return $this->location;
    }

    public function setLocation(?Point $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getLocationLatitude(): ?float
    {
        return $this->location_latitude;
    }

    public function setLocationLatitude(float $location_latitude): self
    {
        $this->location_latitude = $location_latitude;

        return $this;
    }

    public function getLocationLongitude(): ?float
    {
        return $this->location_longitude;
    }

    public function setLocationLongitude(float $location_longitude): self
    {
        $this->location_longitude = $location_longitude;

        return $this;
    }
}
