<?php

namespace App\Entity;

use App\Repository\MesureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MesureRepository::class)]
class Mesure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $dateSaisie = null;

    #[ORM\Column]
    private ?float $valeur = null;

    #[ORM\ManyToOne(inversedBy: 'mesures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Aquarium $aquarium = null;

    #[ORM\ManyToOne(inversedBy: 'mesures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alerte $alerte = null;

    #[ORM\ManyToOne(inversedBy: 'mesures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateSaisie(): ?\DateTime
    {
        return $this->dateSaisie;
    }

    public function setDateSaisie(\DateTime $dateSaisie): static
    {
        $this->dateSaisie = $dateSaisie;

        return $this;
    }

    public function getValeur(): ?float
    {
        return $this->valeur;
    }

    public function setValeur(float $valeur): static
    {
        $this->valeur = $valeur;

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
