<?php

namespace App\Entity;

use App\Repository\PassageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Passage extends User
{
    #[ORM\ManyToOne(inversedBy: 'passager')]
    private ?CovoiturageCond $covoiturageCond = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'passager')]
    private Collection $reservations;

    #[ORM\Column(nullable: true)]
    private ?int $numtel = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getCovoiturageCond(): ?CovoiturageCond
    {
        return $this->covoiturageCond;
    }

    public function setCovoiturageCond(?CovoiturageCond $covoiturageCond): static
    {
        $this->covoiturageCond = $covoiturageCond;

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
            $reservation->setPassager($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getPassager() === $this) {
                $reservation->setPassager(null);
            }
        }

        return $this;
    }

    public function getNumtel(): ?int
    {
        return $this->numtel;
    }

    public function setNumtel(?int $numtel): static
    {
        $this->numtel = $numtel;

        return $this;
    }
}
