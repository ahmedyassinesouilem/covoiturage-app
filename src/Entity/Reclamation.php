<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    private ?Conducteur $conducteur = null;

    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    private ?Passage $passager = null;

    #[ORM\Column(length: 1000)]
    private ?string $objet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConducteur(): ?Conducteur
    {
        return $this->conducteur;
    }

    public function setConducteur(?Conducteur $conducteur): static
    {
        $this->conducteur = $conducteur;

        return $this;
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

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): static
    {
        $this->objet = $objet;

        return $this;
    }
}
