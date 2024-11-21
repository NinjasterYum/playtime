<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(type: 'date')]
    #[Assert\NotNull(message: "Veuillez sélectionner une date.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date doit être aujourd'hui ou dans le futur.")]
    private ?\DateTimeInterface $date = null;
    

    #[ORM\Column(type: 'time')]
    #[Assert\NotNull(message: "Veuillez sélectionner une heure.")]
    private ?\DateTimeInterface $time = null;
    
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: true)]
    private ?StandardUser $standardUser = null;

    #[ORM\ManyToOne(inversedBy: 'reservations', targetEntity: SportCompany::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?SportCompany $sportCompany = null;

    #[ORM\ManyToOne(inversedBy: 'reservations', targetEntity: Service::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Veuillez sélectionner un service.")]
    private ?Service $service = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ['pending', 'validated', 'cancelled'], message: "Veuillez sélectionner un statut valide.")]
    private ?string $status = 'pending';

    #[ORM\ManyToOne(targetEntity: Terrain::class ,inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Terrain $terrain = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStandardUser(): ?StandardUser
    {
        return $this->standardUser;
    }

    public function setStandardUser(?StandardUser $standardUser): static
    {
        $this->standardUser = $standardUser;
        return $this;
    }

    public function getSportCompany(): ?SportCompany
    {
        return $this->sportCompany;
    }

    public function setSportCompany(?SportCompany $sportCompany): static
    {
        $this->sportCompany = $sportCompany;
        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }


    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;
        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        if ($this->date === null || $this->time === null) {
            return null;
        }
        return new \DateTime($this->date->format('Y-m-d') . ' ' . $this->time->format('H:i:s'));
    }

    public function setDateTime(\DateTimeInterface $dateTime): self
    {
        $this->date = $dateTime;
        $this->time = $dateTime;
        return $this;
    }

    public function getTerrain(): ?Terrain
    {
        return $this->terrain;
    }

    public function setTerrain(?Terrain $terrain): self
    {
        $this->terrain = $terrain;
        return $this;
    }
    
}