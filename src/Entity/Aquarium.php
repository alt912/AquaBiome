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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $derniereMaj = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dernierChangementEau = null;

    #[ORM\OneToMany(targetEntity: PoissonInventaire::class, mappedBy: 'aquarium')]
    private Collection $poissonInventaires;

    #[ORM\OneToMany(targetEntity: Alerte::class, mappedBy: 'aquarium')]
    private Collection $alertes;

    #[ORM\OneToMany(targetEntity: Tache::class, mappedBy: 'aquarium')]
    private Collection $taches;

    #[ORM\OneToMany(targetEntity: Mesure::class, mappedBy: 'aquarium')]
    private Collection $mesures;

    #[ORM\OneToMany(targetEntity: Nourriture::class, mappedBy: 'aquarium')]
    private Collection $nourritures;

    public function __construct()
    {
        $this->poissonInventaires = new ArrayCollection();
        $this->alertes = new ArrayCollection();
        $this->taches = new ArrayCollection();
        $this->mesures = new ArrayCollection();
        $this->nourritures = new ArrayCollection();
        $this->derniereMaj = new \DateTime();
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

    public function setDescription(?string $description): static
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

    public function getDerniereMaj(): ?\DateTimeInterface
    {
        return $this->derniereMaj;
    }

    public function setDerniereMaj(\DateTimeInterface $derniereMaj): static
    {
        $this->derniereMaj = $derniereMaj;
        return $this;
    }

    public function getDernierChangementEau(): ?\DateTimeInterface
    {
        return $this->dernierChangementEau;
    }

    public function setDernierChangementEau(?\DateTimeInterface $dernierChangementEau): static
    {
        $this->dernierChangementEau = $dernierChangementEau;
        return $this;
    }

    public function getPoissonInventaires(): Collection
    {
        return $this->poissonInventaires;
    }

    public function getAlertes(): Collection
    {
        return $this->alertes;
    }

    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function getMesures(): Collection
    {
        return $this->mesures;
    }

    public function getNourritures(): Collection
    {
        return $this->nourritures;
    }

    /**
     * Permet à EasyAdmin d'afficher le nom de l'aquarium dans les listes
     */
    public function __toString(): string
    {
        return $this->nom ?? 'Nouvel Aquarium';
    }
}