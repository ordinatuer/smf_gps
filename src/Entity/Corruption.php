<?php

namespace App\Entity;

use App\Repository\CorruptionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\geo\Objects\Point;

#[ORM\Entity(repositoryClass: CorruptionRepository::class)]
class Corruption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $ya_id;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private $first_name;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private $full_name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $email;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private $phone_number;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private $address_city;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private $address_street;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private $address_house;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private $address_entrance;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private $address_floor;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private $address_office;

    #[ORM\Column(type: 'text', nullable: true)]
    private $address_comment;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private $address_doorcode;

    #[ORM\Column(type: 'point', nullable: true)]
    private $location;

    #[ORM\Column(type: 'text', nullable: true)]
    private $location_latitude;

    #[ORM\Column(type: 'text', nullable: true)]
    private $location_longitude;

    #[ORM\Column(type: 'float')]
    private $amount_charged;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $user_id;

    #[ORM\Column(type: 'text', nullable: true)]
    private $user_agent;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $created_at;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $point_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYaId(): ?int
    {
        return $this->ya_id;
    }

    public function setYaId(int $ya_id): self
    {
        $this->ya_id = $ya_id;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(?string $full_name): self
    {
        $this->full_name = $full_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(?string $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getAddressCity(): ?string
    {
        return $this->address_city;
    }

    public function setAddressCity(?string $address_city): self
    {
        $this->address_city = $address_city;

        return $this;
    }

    public function getAddressStreet(): ?string
    {
        return $this->address_street;
    }

    public function setAddressStreet(?string $address_street): self
    {
        $this->address_street = $address_street;

        return $this;
    }

    public function getAddressHouse(): ?string
    {
        return $this->address_house;
    }

    public function setAddressHouse(?string $address_house): self
    {
        $this->address_house = $address_house;

        return $this;
    }

    public function getAddressEntrance(): ?string
    {
        return $this->address_entrance;
    }

    public function setAddressEntrance(?string $address_entrance): self
    {
        $this->address_entrance = $address_entrance;

        return $this;
    }

    public function getAddressFloor(): ?string
    {
        return $this->address_floor;
    }

    public function setAddressFloor(string $address_floor): self
    {
        $this->address_floor = $address_floor;

        return $this;
    }

    public function getAddressOffice(): ?string
    {
        return $this->address_office;
    }

    public function setAddressOffice(?string $address_office): self
    {
        $this->address_office = $address_office;

        return $this;
    }

    public function getAddressComment(): ?string
    {
        return $this->address_comment;
    }

    public function setAddressComment(?string $address_comment): self
    {
        $this->address_comment = $address_comment;

        return $this;
    }

    public function getAddressDoorcode(): ?string
    {
        return $this->address_doorcode;
    }

    public function setAddressDoorcode(?string $address_doorcode): self
    {
        $this->address_doorcode = $address_doorcode;

        return $this;
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

    public function getLocationLatitude(): ?string
    {
        return $this->location_latitude;
    }

    public function setLocationLatitude(?string $location_latitude): self
    {
        $this->location_latitude = $location_latitude;

        return $this;
    }

    public function getLocationLongitude(): ?string
    {
        return $this->location_longitude;
    }

    public function setLocationLongitude(?string $location_longitude): self
    {
        $this->location_longitude = $location_longitude;

        return $this;
    }

    public function getAmountCharged(): ?float
    {
        return $this->amount_charged;
    }

    public function setAmountCharged(float $amount_charged): self
    {
        $this->amount_charged = $amount_charged;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function setUserAgent(?string $user_agent): self
    {
        $this->user_agent = $user_agent;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPointId(): ?int
    {
        return $this->point_id;
    }

    public function setPointId(?int $point_id): self
    {
        $this->point_id = $point_id;

        return $this;
    }

}
