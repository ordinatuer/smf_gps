<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use App\geo\Objects\Point;
use App\geo\Types\PostGISType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    const DEFAULT_CITY = 'Санкт-Петербург';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $city;

    #[ORM\Column(type: 'string', length: 255)]
    private $street;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $house;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $build;

    #[ORM\Column(type: 'string', length: 255)]
    private $address;

    #[ORM\Column(type: 'float')]
    private $lon;

    #[ORM\Column(type: 'float')]
    private $lat;

    #[ORM\Column(type: PostGISType::GEOGRAPHY, options:['geometry_type' => 'POINT'])]
    private $location;

    #[ORM\Column(type: 'integer', options : ['default' => 1])]
    private $operator;

    #[ORM\Column(type: 'text')]
    private $api_response;

    #[ORM\ManyToOne(targetEntity: Yafile::class, inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private $file;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDefaultCity()
    {
        $this->city = self::DEFAULT_CITY;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getHouse(): ?string
    {
        return $this->house;
    }

    public function setHouse(string $house): self
    {
        $this->house = $house;

        return $this;
    }

    public function getBuild(): ?string
    {
        return $this->build;
    }

    public function setBuild(string $build): self
    {
        $this->build = $build;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getOperator(): ?int
    {
        return $this->operator;
    }

    public function setOperator(int $operator): self
    {
        $this->operator = $operator;

        return $this;
    }

    public function getApiResponse(): ?string
    {
        return $this->api_response;
    }

    public function setApiResponse(string $api_response): self
    {
        $this->api_response = $api_response;

        return $this;
    }

    public function getFile(): ?Yafile
    {
        return $this->file;
    }

    public function setFile(?Yafile $file): self
    {
        $this->file = $file;

        return $this;
    }
}
