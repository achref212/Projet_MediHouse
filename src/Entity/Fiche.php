<?php

namespace App\Entity;

use App\Repository\FicheRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheRepository::class)]
class Fiche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $Age = null;

    #[ORM\Column(length: 255)]
    private ?string $BloodType = null;

    #[ORM\ManyToOne(inversedBy: 'fiches')]
    private ?User $Patient = null;

    #[ORM\OneToMany(mappedBy: 'fiche', targetEntity: RendezVous::class)]
    private Collection $RendezVous;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $docteur = null;

    public function __construct()
    {
        $this->RendezVous = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAge(): ?int
    {
        return $this->Age;
    }

    public function setAge(int $Age): self
    {
        $this->Age = $Age;

        return $this;
    }

    public function getBloodType(): ?string
    {
        return $this->BloodType;
    }

    public function setBloodType(string $BloodType): self
    {
        $this->BloodType = $BloodType;

        return $this;
    }

    public function getPatient(): ?User
    {
        return $this->Patient;
    }

    public function setPatient(?User $Patient): self
    {
        $this->Patient = $Patient;

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVous(): Collection
    {
        return $this->RendezVous;
    }

    public function addRendezVou(RendezVous $rendezVou): self
    {
        if (!$this->RendezVous->contains($rendezVou)) {
            $this->RendezVous->add($rendezVou);
            $rendezVou->setFiche($this);
        }

        return $this;
    }

    public function removeRendezVou(RendezVous $rendezVou): self
    {
        if ($this->RendezVous->removeElement($rendezVou)) {
            // set the owning side to null (unless already changed)
            if ($rendezVou->getFiche() === $this) {
                $rendezVou->setFiche(null);
            }
        }

        return $this;
    }

    public function getDocteur(): ?User
    {
        return $this->docteur;
    }

    public function setDocteur(?User $docteur): self
    {
        $this->docteur = $docteur;

        return $this;
    }
}
