<?php

namespace App\Entity;

use App\Repository\StandardUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StandardUserRepository::class)]
class StandardUser extends User
{
    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\OneToMany(mappedBy: 'standardUser', targetEntity: Reservation::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
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
            $reservation->setStandardUser($this);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getStandardUser() === $this) {
                $reservation->setStandardUser(null);
            }
        }
        return $this;
    }
}