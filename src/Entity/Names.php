<?php

namespace App\Entity;

use App\Repository\NamesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NamesRepository::class)]
class Names
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private $lastname;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }
}
