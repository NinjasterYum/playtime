<?php

namespace App\Entity;

use App\Repository\SportCompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SportCompanyRepository::class)]
class SportCompany extends User
{
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'sportCompany', targetEntity: CompanyImage::class, cascade: ['persist', 'remove'])]
    private Collection $images;

    #[ORM\Column]
    private ?bool $isSubscribed = false;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $longitude = null;

    #[ORM\OneToMany(mappedBy: 'sportCompany', targetEntity: Service::class, cascade: ['persist', 'remove'])]
    private Collection $services;
    
    #[ORM\OneToMany(mappedBy: 'sportCompany', targetEntity: Terrain::class, cascade: ['persist', 'remove'])]
    private Collection $terrains;
    
    #[ORM\OneToMany(mappedBy: 'sportCompany', targetEntity: Schedule::class, cascade: ['persist', 'remove'])]
    private Collection $schedules;
    
    #[ORM\OneToMany(mappedBy: 'sportCompany', targetEntity: Reservation::class, cascade: ['persist', 'remove'])]
    private Collection $reservations;

    #[ORM\Column(length: 255)]
    private ?string $PostalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $City = null;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->terrains = new ArrayCollection();
        $this->schedules = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getIsSubscribed(): ?bool
    {
        return $this->isSubscribed;
    }

    public function setIsSubscribed(?bool $isSubscribed): static
    {
        $this->isSubscribed = $isSubscribed;
        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setSportCompany($this);
        }
        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            if ($service->getSportCompany() === $this) {
                $service->setSportCompany(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Schedule>
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): static
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules->add($schedule);
            $schedule->setSportCompany($this);
        }
        return $this;
    }

    public function removeSchedule(Schedule $schedule): static
    {
        if ($this->schedules->removeElement($schedule)) {
            if ($schedule->getSportCompany() === $this) {
                $schedule->setSportCompany(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setSportCompany($this);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getSportCompany() === $this) {
                $reservation->setSportCompany(null);
            }
        }
        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->PostalCode;
    }

    public function setPostalCode(string $PostalCode): static
    {
        $this->PostalCode = $PostalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->City;
    }

    public function setCity(string $City): static
    {
        $this->City = $City;

        return $this;
    }

    /**
     * @return Collection<int, CompanyImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(CompanyImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setSportCompany($this);
        }
        return $this;
    }

    public function removeImage(CompanyImage $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getSportCompany() === $this) {
                $image->setSportCompany(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Terrain>
     */
    public function getTerrains(): Collection
    {
        return $this->terrains;
    }

    public function addTerrain(Terrain $terrain): static
    {
        if (!$this->terrains->contains($terrain)) {
            $this->terrains->add($terrain);
            $terrain->setSportCompany($this);
        }
        return $this;
    }

    public function removeTerrain(Terrain $terrain): static
    {
        if ($this->terrains->removeElement($terrain)) {
            if ($terrain->getSportCompany() === $this) {
                $terrain->setSportCompany(null);
            }
        }
        return $this;
    }
}