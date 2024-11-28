<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $nbpplace = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $model = null;

    #[ORM\ManyToOne(inversedBy: 'vehicules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Conducteur $conducteur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getNbpplace(): ?int
    {
        return $this->nbpplace;
    }

    public function setNbpplace(int $nbpplace): static
    {
        $this->nbpplace = $nbpplace;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): static
    {
        $this->model = $model;

        return $this;
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

}
