<?php

namespace App\Entity;

use App\Repository\AlerteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlerteRepository::class)]
class Alerte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $unite = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $messageAlerte = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateAlerte = null;

    #[ORM\ManyToOne(inversedBy: 'alertes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Aquarium $aquarium = null;

    #[ORM\OneToMany(targetEntity: Mesure::class, mappedBy: 'alerte')]
    private Collection $mesures;

    public function __construct()
    {
        $this->mesures = new ArrayCollection();
        // La date se met automatiquement à "maintenant"
        $this->dateAlerte = new \DateTime(); 
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

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(?string $unite): static
    {
        $this->unite = $unite;
        return $this;
    }

    public function getMessageAlerte(): ?string
    {
        return $this->messageAlerte;
    }

    public function setMessageAlerte(string $messageAlerte): static
    {
        $this->messageAlerte = $messageAlerte;
        return $this;
    }

    public function getDateAlerte(): ?\DateTimeInterface
    {
        return $this->dateAlerte;
    }

    public function setDateAlerte(\DateTimeInterface $dateAlerte): static
    {
        $this->dateAlerte = $dateAlerte;
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

    public function getMesures(): Collection
    {
        return $this->mesures;
    }
}