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

    #[ORM\Column(length: 255)]
    private ?string $unite = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $messageAlerte = null;

    #[ORM\Column]
    private ?\DateTime $dateAlerte = null;

    #[ORM\ManyToOne(inversedBy: 'alertes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Aquarium $aquarium = null;

    /**
     * @var Collection<int, Mesure>
     */
    #[ORM\OneToMany(targetEntity: Mesure::class, mappedBy: 'alerte')]
    private Collection $mesures;

    public function __construct()
    {
        $this->mesures = new ArrayCollection();
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

    public function setUnite(string $unite): static
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

    public function getDateAlerte(): ?\DateTime
    {
        return $this->dateAlerte;
    }

    public function setDateAlerte(\DateTime $dateAlerte): static
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
            $mesure->setAlerte($this);
        }

        return $this;
    }

    public function removeMesure(Mesure $mesure): static
    {
        if ($this->mesures->removeElement($mesure)) {
            // set the owning side to null (unless already changed)
            if ($mesure->getAlerte() === $this) {
                $mesure->setAlerte(null);
            }
        }

        return $this;
    }
}
