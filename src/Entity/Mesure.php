<?php

namespace App\Entity;

use App\Repository\MesureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MesureRepository::class)]
class Mesure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateSaisie = null;

    #[ORM\Column]
    private ?float $temperature = null;

    #[ORM\Column]
    private ?float $ph = null;

    #[ORM\Column(nullable: true)]
    private ?float $chlore = null;

    #[ORM\Column(nullable: true)]
    private ?int $gh = null;

    #[ORM\Column(nullable: true)]
    private ?int $kh = null;

    #[ORM\Column(nullable: true)]
    private ?float $valeur = null;

    // --- NOUVELLES COLONNES POUR LES GRAPHIQUES ---
    #[ORM\Column(nullable: true)]
    private ?float $nitrites = null;

    #[ORM\Column(nullable: true)]
    private ?float $ammonium = null;
    // ----------------------------------------------

    #[ORM\ManyToOne(targetEntity: Aquarium::class, inversedBy: 'mesures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Aquarium $aquarium = null;

<<<<<<< Updated upstream
    #[ORM\ManyToOne(inversedBy: 'mesures')]
<<<<<<< HEAD
    #[ORM\JoinColumn(nullable: false)]
=======
    #[ORM\ManyToOne(targetEntity: Alerte::class, inversedBy: 'mesures')]
    #[ORM\JoinColumn(nullable: true)]
>>>>>>> Stashed changes
=======
    #[ORM\JoinColumn(nullable: true)]
>>>>>>> 4f60feea152426b9dc2fbc2a861b22f101341185
    private ?Alerte $alerte = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'mesures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateSaisie(): ?\DateTimeInterface
    {
        return $this->dateSaisie;
    }

    public function setDateSaisie(\DateTimeInterface $dateSaisie): static
    {
        $this->dateSaisie = $dateSaisie;
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

    public function getPh(): ?float
    {
        return $this->ph;
    }

    public function setPh(float $ph): static
    {
        $this->ph = $ph;
        return $this;
    }

    public function getChlore(): ?float
    {
        return $this->chlore;
    }

    public function setChlore(?float $chlore): static
    {
        $this->chlore = $chlore;
        return $this;
    }

    public function getGh(): ?int
    {
        return $this->gh;
    }

    public function setGh(?int $gh): static
    {
        $this->gh = $gh;
        return $this;
    }

    public function getKh(): ?int
    {
        return $this->kh;
    }

    public function setKh(?int $kh): static
    {
        $this->kh = $kh;
        return $this;
    }

    public function getValeur(): ?float
    {
        return $this->valeur;
    }

    public function setValeur(?float $valeur): static
    {
        $this->valeur = $valeur;
        return $this;
    }

    // --- GETTERS & SETTERS POUR NITRITES ---
    public function getNitrites(): ?float
    {
        return $this->nitrites;
    }

    public function setNitrites(?float $nitrites): static
    {
        $this->nitrites = $nitrites;
        return $this;
    }

    // --- GETTERS & SETTERS POUR AMMONIUM ---
    public function getAmmonium(): ?float
    {
        return $this->ammonium;
    }

    public function setAmmonium(?float $ammonium): static
    {
        $this->ammonium = $ammonium;
        return $this;
    }

    public function getAquarium(): ?Aquarium
    {
        return $this->aquarium;
    }

    public function setAquarium(?Aquarium $aquarium): static
    {
        $this->aquarium = $aquarium;
        return $this;
    }

    public function getAlerte(): ?Alerte
    {
        return $this->alerte;
    }

    public function setAlerte(?Alerte $alerte): static
    {
        $this->alerte = $alerte;
        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }
}