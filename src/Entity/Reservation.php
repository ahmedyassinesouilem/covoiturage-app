<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Passage $passager = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?CovoiturageCond $covoiturage = null;

    #[ORM\Column(length: 255)]
    private ?string $etat ="en attend...";

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassager(): ?Passage
    {
        return $this->passager;
    }

    public function setPassager(?Passage $passager): static
    {
        $this->passager = $passager;

        return $this;
    }

    public function getCovoiturage(): ?CovoiturageCond
    {
        return $this->covoiturage;
    }

    public function setCovoiturage(?CovoiturageCond $covoiturage): static
    {
        $this->covoiturage = $covoiturage;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
}
