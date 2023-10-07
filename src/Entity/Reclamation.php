<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("reclamation_list")]
    private ?int $id = null;


    #[ORM\ManyToOne]
    private ?User $User = null;




    #[ORM\Column(length: 255)]
    #[Assert\Email(message: 'Veuillez saisir une adresse email valide.')]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Groups("reclamation_list")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Groups("reclamation_list")]
    private ?string $sujet = null;

    #[ORM\Column(length: 255)]

    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Groups("reclamation_list")]
    private ?string $descreption = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups("reclamation_list")]
    private ?\DateTimeInterface $date_reclamation = null;

    #[ORM\Column(length: 255)]
    #[Groups("reclamation_list")]
    public ?string $etat = null;

    #[ORM\OneToOne(mappedBy: 'reclamation', cascade: ['persist', 'remove'])]
    private ?Reponse1 $Reponse1 = null;

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $user): self
    {
        $this->User = $user;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getDescreption(): ?string
    {
        return $this->descreption;
    }

    public function setDescreption(string $descreption): self
    {
        $this->descreption = $descreption;

        return $this;
    }

    public function getDateReclamation(): ?\DateTimeInterface
    {
        return $this->date_reclamation;
    }

    public function setDateReclamation(\DateTimeInterface $date_reclamation): self
    {
        $this->date_reclamation = $date_reclamation;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat ?? 'non-traité';

        return $this;
    }

    public function getReponse1(): ?Reponse1
    {
        return $this->Reponse1;
    }

    public function setReponse1(?Reponse1 $Reponse1): self
    {
        // unset the owning side of the relation if necessary
        //  if ($Reponse1 === null && $this->Reponse1 !== null) {
        //     $this->Reponse1->setReclamation(null);
        //  }

        // set the owning side of the relation if necessary
        if ($Reponse1 !== null && $Reponse1->getReclamation() !== $this) {
            $Reponse1->setReclamation($this);
        }

        $this->Reponse1 = $Reponse1;

        return $this;
    }
}
