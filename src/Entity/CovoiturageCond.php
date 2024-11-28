<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Repository\CovoiturageCondRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CovoiturageCondRepository::class)]
class CovoiturageCond
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'covoiturages')]
    private ?Conducteur $conducteur = null;

    #[ORM\Column(length: 255)]
    private ?string $villedeDepart = null;

    #[ORM\Column(length: 255)]
    private ?string $villeArriver = null;

    #[ORM\Column(length: 255)]
    private ?string $placedeRencontre = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicule $vehicule = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;


    /**
     * @var Collection<int, Passage>
     */
    #[ORM\OneToMany(targetEntity: Passage::class, mappedBy: 'covoiturageCond')]
    private Collection $passager;

    #[ORM\Column]
    private ?bool $IsStaurer = false;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'covoiturage')]
    private Collection $reservations;

    #[ORM\Column]
    private ?int $nbplace = 0;

    public function __construct()
    {
        $this->passager = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

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

    public function getVilledeDepart(): ?string
    {
        return $this->villedeDepart;
    }

    public function setVilledeDepart(string $villedeDepart): static
    {
        $this->villedeDepart = $villedeDepart;

        return $this;
    }

    public function getVilleArriver(): ?string
    {
        return $this->villeArriver;
    }

    public function setVilleArriver(string $villeArriver): static
    {
        $this->villeArriver = $villeArriver;

        return $this;
    }

    public function getPlacedeRencontre(): ?string
    {
        return $this->placedeRencontre;
    }

    public function setPlacedeRencontre(string $placedeRencontre): static
    {
        $this->placedeRencontre = $placedeRencontre;

        return $this;
    }

    public function getVehicule(): ?Vehicule
    {
        return $this->vehicule;
    }

    public function setVehicule(Vehicule $vehicule): static
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }
    /**
     * @return Collection<int, Passage>
     */
    public function getPassager(): Collection
    {
        return $this->passager;
    }

    public function addPassager(Passage $passager): static
    {
        if (!$this->passager->contains($passager)) {
            $this->passager->add($passager);
            $passager->setCovoiturageCond($this);
        }

        return $this;
    }

    public function removePassager(Passage $passager): static
    {
        if ($this->passager->removeElement($passager)) {
            // set the owning side to null (unless already changed)
            if ($passager->getCovoiturageCond() === $this) {
                $passager->setCovoiturageCond(null);
            }
        }

        return $this;
    }

    public function isStaurer(): ?bool
    {
        return $this->IsStaurer;
    }

    public function setStaurer(bool $IsStaurer): static
    {
        $this->IsStaurer = $IsStaurer;

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
            $reservation->setCovoiturage($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getCovoiturage() === $this) {
                $reservation->setCovoiturage(null);
            }
        }

        return $this;
    }

    public function getNbplace(): ?int
    {
        return $this->nbplace;
    }

    public function setNbplace(int $nbplace): static
    {
        $this->nbplace = $nbplace;

        return $this;
    }
    #[Assert\Callback]
public function validateNbplace(ExecutionContextInterface $context): void
{
    if ($this->vehicule && $this->nbplace > $this->vehicule->getNbpplace()) {
        $context->buildViolation('Le nombre de places du covoiturage ne peut pas dépasser le nombre de places du véhicule.')
            ->atPath('nbplace') // Spécifie le champ lié à l'erreur
            ->addViolation();
    }
}
public function decrementNbplace(): void
{
    if ($this->nbplace > 0) {
        $this->nbplace--;
    }
}

}
