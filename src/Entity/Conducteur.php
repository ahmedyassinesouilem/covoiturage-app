<?php

namespace App\Entity;

use App\Repository\ConducteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Conducteur extends User
{
    #[ORM\Column]
    private ?int $numPermis = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Adress = null;

    /**
     * @var Collection<int, CovoiturageCond>
     */
    #[ORM\OneToMany(targetEntity: CovoiturageCond::class, mappedBy: 'conducteur')]
    private Collection $covoiturages;

    /**
     * @var Collection<int, Vehicule>
     */
    #[ORM\OneToMany(targetEntity: Vehicule::class, mappedBy: 'conducteur')]
    private Collection $vehicules;

    public function __construct()
    {
        $this->covoiturages = new ArrayCollection();
        $this->vehicules = new ArrayCollection();
    }

    public function getNumPermis(): ?int
    {
        return $this->numPermis;
    }

    public function setNumPermis(int $numPermis): static
    {
        $this->numPermis = $numPermis;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->Adress;
    }

    public function setAdress(?string $Adress): static
    {
        $this->Adress = $Adress;

        return $this;
    }

    /**
     * @return Collection<int, CovoiturageCond>
     */
    public function getCovoiturages(): Collection
    {
        return $this->covoiturages;
    }

    public function addCovoiturage(CovoiturageCond $covoiturage): static
    {
        if (!$this->covoiturages->contains($covoiturage)) {
            $this->covoiturages->add($covoiturage);
            $covoiturage->setConducteur($this);
        }

        return $this;
    }

    public function removeCovoiturage(CovoiturageCond $covoiturage): static
    {
        if ($this->covoiturages->removeElement($covoiturage)) {
            // set the owning side to null (unless already changed)
            if ($covoiturage->getConducteur() === $this) {
                $covoiturage->setConducteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vehicule>
     */
    public function getVehicules(): Collection
    {
        return $this->vehicules;
    }

    public function addVehicule(Vehicule $vehicule): static
    {
        if (!$this->vehicules->contains($vehicule)) {
            $this->vehicules->add($vehicule);
            $vehicule->setConducteur($this);
        }

        return $this;
    }

    public function removeVehicule(Vehicule $vehicule): static
    {
        if ($this->vehicules->removeElement($vehicule)) {
            // set the owning side to null (unless already changed)
            if ($vehicule->getConducteur() === $this) {
                $vehicule->setConducteur(null);
            }
        }

        return $this;
    }
}
