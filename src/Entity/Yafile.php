<?php

namespace App\Entity;

use App\Repository\YafileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: YafileRepository::class)]
class Yafile
{
    /**
     * @defult value for status
     */
    const LOAD_NOT_PARSED = 1;
    const LOAD_PARSED = 2;
    const LOAD_PARSE_IN_PROGRESS = 3;
    const LOAD_LOST = 4;
    const PARSE_ERROR = 5;
    const FILE_OPEN_ERROR = 6;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'datetime')]
    private $added;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $description;

    #[ORM\Column(type: 'smallint')]
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAdded(): ?\DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(\DateTimeInterface $added): self
    {
        $this->added = $added;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
