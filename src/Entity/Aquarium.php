<?php

namespace App\Entity;

use App\Repository\AquariumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AquariumRepository::class)]
class Aquarium
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $typeEau = null;

    #[ORM\Column]
    private ?float $temperature = null;

    #[ORM\Column]
    private ?float $volumeLitre = null;

    #[ORM\Column]
    private ?\DateTime $derniereMaj = null;

    #[ORM\Column]
    private ?\DateTime $dernierChangementEau = null;

    /**
     * @var Collection<int, PoissonInventaire>
     */
    #[ORM\OneToMany(targetEntity: PoissonInventaire::class, mappedBy: 'aquarium')]
    private Collection $poissonInventaires;

    /**
     * @var Collection<int, Alerte>
     */
    #[ORM\OneToMany(targetEntity: Alerte::class, mappedBy: 'aquarium')]
    private Collection $alertes;

    /**
     * @var Collection<int, Tache>
     */
    #[ORM\OneToMany(targetEntity: Tache::class, mappedBy: 'aquarium')]
    private Collection $taches;

    /**
     * @var Collection<int, Mesure>
     */
    #[ORM\OneToMany(targetEntity: Mesure::class, mappedBy: 'aquarium')]
    private Collection $mesures;

    /**
     * @var Collection<int, Nourriture>
     */
    #[ORM\OneToMany(targetEntity: Nourriture::class, mappedBy: 'Aquarium')]
    private Collection $nourritures;

    public function __construct()
    {
        $this->poissonInventaires = new ArrayCollection();
        $this->alertes = new ArrayCollection();
        $this->taches = new ArrayCollection();
        $this->mesures = new ArrayCollection();
        $this->nourritures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTypeEau(): ?string
    {
        return $this->typeEau;
    }

    public function setTypeEau(string $typeEau): static
    {
        $this->typeEau = $typeEau;

        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getVolumeLitre(): ?float
    {
        return $this->volumeLitre;
    }

    public function setVolumeLitre(float $volumeLitre): static
    {
        $this->volumeLitre = $volumeLitre;

        return $this;
    }

    public function getDerniereMaj(): ?\DateTime
    {
        return $this->derniereMaj;
    }

    public function setDerniereMaj(\DateTime $derniereMaj): static
    {
        $this->derniereMaj = $derniereMaj;

        return $this;
    }

    public function getDernierChangementEau(): ?\DateTime
    {
        return $this->dernierChangementEau;
    }

    public function setDernierChangementEau(\DateTime $dernierChangementEau): static
    {
        $this->dernierChangementEau = $dernierChangementEau;

        return $this;
    }

    /**
     * @return Collection<int, PoissonInventaire>
     */
    public function getPoissonInventaires(): Collection
    {
        return $this->poissonInventaires;
    }

    public function addPoissonInventaire(PoissonInventaire $poissonInventaire): static
    {
        if (!$this->poissonInventaires->contains($poissonInventaire)) {
            $this->poissonInventaires->add($poissonInventaire);
            $poissonInventaire->setAquarium($this);
        }

        return $this;
    }

    public function removePoissonInventaire(PoissonInventaire $poissonInventaire): static
    {
        if ($this->poissonInventaires->removeElement($poissonInventaire)) {
            // set the owning side to null (unless already changed)
            if ($poissonInventaire->getAquarium() === $this) {
                $poissonInventaire->setAquarium(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Alerte>
     */
    public function getAlertes(): Collection
    {
        return $this->alertes;
    }

    public function addAlerte(Alerte $alerte): static
    {
        if (!$this->alertes->contains($alerte)) {
            $this->alertes->add($alerte);
            $alerte->setAquarium($this);
        }

        return $this;
    }

    public function removeAlerte(Alerte $alerte): static
    {
        if ($this->alertes->removeElement($alerte)) {
            // set the owning side to null (unless already changed)
            if ($alerte->getAquarium() === $this) {
                $alerte->setAquarium(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tache>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTach(Tache $tach): static
    {
        if (!$this->taches->contains($tach)) {
            $this->taches->add($tach);
            $tach->setAquarium($this);
        }

        return $this;
    }

    public function removeTach(Tache $tach): static
    {
        if ($this->taches->removeElement($tach)) {
            // set the owning side to null (unless already changed)
            if ($tach->getAquarium() === $this) {
                $tach->setAquarium(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mesure>
     */
    public function getMesures(): Collection
    {
        return $this->mesures;
    }

    public function addMesure(Mesure $mesure): static
    {
        if (!$this->mesures->contains($mesure)) {
            $this->mesures->add($mesure);
            $mesure->setAquarium($this);
        }

        return $this;
    }

    public function removeMesure(Mesure $mesure): static
    {
        if ($this->mesures->removeElement($mesure)) {
            // set the owning side to null (unless already changed)
            if ($mesure->getAquarium() === $this) {
                $mesure->setAquarium(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Nourriture>
     */
    public function getNourritures(): Collection
    {
        return $this->nourritures;
    }

    public function addNourriture(Nourriture $nourriture): static
    {
        if (!$this->nourritures->contains($nourriture)) {
            $this->nourritures->add($nourriture);
            $nourriture->setAquarium($this);
        }

        return $this;
    }

    public function removeNourriture(Nourriture $nourriture): static
    {
        if ($this->nourritures->removeElement($nourriture)) {
            // set the owning side to null (unless already changed)
            if ($nourriture->getAquarium() === $this) {
                $nourriture->setAquarium(null);
            }
        }

        return $this;
    }
}
