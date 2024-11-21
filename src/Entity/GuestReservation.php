<?php

namespace App\Entity;

use App\Repository\GuestReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GuestReservationRepository::class)]
class GuestReservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Service::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $service;

    #[ORM\Column(type: 'datetime')]
    private $dateTime;

    #[ORM\ManyToOne(inversedBy: 'reservations', targetEntity: SportCompany::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?SportCompany $sportCompany = null;

    #[ORM\Column(type: 'string', length: 255)]
    private $clientFirstName;

    #[ORM\Column(type: 'string', length: 255)]
    private $clientLastName;

    #[ORM\Column(type: 'string', length: 255)]
    private $clientEmail;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $clientPhone;

    #[ORM\ManyToOne(targetEntity: Terrain::class ,inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Terrain $terrain = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ['pending', 'validated', 'cancelled'], message: "Veuillez sélectionner un statut valide.")]
    private ?string $status = 'pending';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;
        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;
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

    public function getClientFirstName(): ?string
    {
        return $this->clientFirstName;
    }

    public function setClientFirstName(string $clientFirstName): self
    {
        $this->clientFirstName = $clientFirstName;
        return $this;
    }

    public function getClientLastName(): ?string
    {
        return $this->clientLastName;
    }

    public function setClientLastName(string $clientLastName): self
    {
        $this->clientLastName = $clientLastName;
        return $this;
    }

    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }

    public function setClientEmail(string $clientEmail): self
    {
        $this->clientEmail = $clientEmail;
        return $this;
    }

    public function getClientPhone(): ?string
    {
        return $this->clientPhone;
    }

    public function setClientPhone(?string $clientPhone): self
    {
        $this->clientPhone = $clientPhone;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }
}