<?php

namespace App\Entity;

use App\Repository\YafileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: YafileRepository::class)]
class Yafile
{
    //FILE_TYPES['PROVIDERS_ADDRESS_LIST']
    const FILE_TYPES = [
        'YAFILE' => 1,
        'PROVIDERS_ADDRESS_LIST' => 2,
    ];

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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'yafiles')]
    private $Yuser;

    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    private $file_type = 1;

    #[ORM\OneToMany(mappedBy: 'file', targetEntity: Address::class, orphanRemoval: true)]
    private $addresses;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

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

    public function getYuser(): ?User
    {
        return $this->Yuser;
    }

    public function setYuser(?User $Yuser): self
    {
        $this->Yuser = $Yuser;

        return $this;
    }

    public function getFileType(): ?int
    {
        return $this->file_type;
    }

    public function setFileType(int $file_type): self
    {
        $this->file_type = $file_type;

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setFile($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getFile() === $this) {
                $address->setFile(null);
            }
        }

        return $this;
    }
}
