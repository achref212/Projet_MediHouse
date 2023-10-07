<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $Local = null;


    #[ORM\Column(length: 255)]
    private ?string $Statut = null;

    #[ORM\ManyToOne(inversedBy: 'RendezVous')]
    private ?Fiche $fiche = null;

    #[ORM\ManyToOne]
    private ?User $docteur = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVouses')]
    private ?User $patient = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $time = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLocal(): ?string
    {
        return $this->Local;
    }

    public function setLocal(string $Local): self
    {
        $this->Local = $Local;

        return $this;
    }





    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(string $Statut): self
    {
        $this->Statut = $Statut;

        return $this;
    }

    public function getFiche(): ?Fiche
    {
        return $this->fiche;
    }

    public function setFiche(?Fiche $fiche): self
    {
        $this->fiche = $fiche;

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

    public function setPatient(?User $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    public function getPatient(): ?User
    {
        return $this->patient;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }
}
