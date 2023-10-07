<?php

namespace App\Entity;

use App\Repository\Reponse1Repository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: Reponse1Repository::class)]
class Reponse1
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[ORM\GeneratedValue]
    public ?int $id_Reponse1 = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $Reponse1Des = null;


    #[ORM\OneToOne(inversedBy: 'Reponse1', cascade: ['persist', 'remove'])]
    private ?Reclamation $reclamation = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdReponse1(): ?int
    {
        return $this->id_Reponse1;
    }

    public function setIdReponse1(?int $id_Reponse1): self
    {
        $this->id_Reponse1 = $id_Reponse1;

        return $this;
    }

    public function getReponse1Des(): ?string
    {
        return $this->Reponse1Des;
    }

    public function setReponse1Des(string $Reponse1Des): self
    {
        $this->Reponse1Des = $Reponse1Des;

        return $this;
    }

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): self
    {
        $this->reclamation = $reclamation;

        return $this;
    }
}
