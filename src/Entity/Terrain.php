<?php

namespace App\Entity;

use App\Repository\TerrainRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TerrainRepository::class)]
class Terrain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\Column]
    private ?bool $isIndoor = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'terrains', targetEntity: SportCompany::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?SportCompany $sportCompany = null;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function isIndoor(): ?bool
    {
        return $this->isIndoor;
    }

    public function setIsIndoor(bool $isIndoor): self
    {
        $this->isIndoor = $isIndoor;
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

    public function getSportCompany(): ?SportCompany
    {
        return $this->sportCompany;
    }

    public function setSportCompany(?SportCompany $sportCompany): self
    {
        $this->sportCompany = $sportCompany;
        return $this;
    }
}