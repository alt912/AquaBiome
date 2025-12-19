<?php

namespace App\Entity;

use App\Repository\NourritureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NourritureRepository::class)]
class Nourriture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $date = null;

    #[ORM\ManyToOne(inversedBy: 'nourritures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Aquarium $Aquarium = null;

    #[ORM\ManyToOne(inversedBy: 'nourritures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getAquarium(): ?Aquarium
    {
        return $this->Aquarium;
    }

    public function setAquarium(?Aquarium $Aquarium): static
    {
        $this->Aquarium = $Aquarium;

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
