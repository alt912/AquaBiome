<?php

namespace App\Entity;

use App\Repository\PoissonInventaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PoissonInventaireRepository::class)]
class PoissonInventaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $especeNom = null;

    #[ORM\Column]
    private ?int $nombre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $remarques = null;

    #[ORM\Column]
    private ?float $phIdealMin = null;

    #[ORM\Column]
    private ?float $phIdealMax = null;

    #[ORM\ManyToOne(inversedBy: 'poissonInventaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Aquarium $aquarium = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEspeceNom(): ?string
    {
        return $this->especeNom;
    }

    public function setEspeceNom(string $especeNom): static
    {
        $this->especeNom = $especeNom;

        return $this;
    }

    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(int $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(?string $remarques): static
    {
        $this->remarques = $remarques;

        return $this;
    }

    public function getPhIdealMin(): ?float
    {
        return $this->phIdealMin;
    }

    public function setPhIdealMin(float $phIdealMin): static
    {
        $this->phIdealMin = $phIdealMin;

        return $this;
    }

    public function getPhIdealMax(): ?float
    {
        return $this->phIdealMax;
    }

    public function setPhIdealMax(float $phIdealMax): static
    {
        $this->phIdealMax = $phIdealMax;

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
}
