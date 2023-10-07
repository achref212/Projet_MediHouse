<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?User $id_user = null;
    /**
     * @Assert\NotBlank(message="champs obligatoire")
     */
    #[ORM\Column(length: 255)]
    private ?string $reponse = null;
    #[ORM\Column(nullable: true)]
    private ?int $likes = null;



    #[ORM\Column(nullable: true)]
    private ?int $disliked = null;
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_pub = null;

    #[ORM\Column(nullable: true)]
    public ?int $id_question = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->date_pub = new \DateTime();
    }

    public function getuser(): ?User
    {
        return $this->id_user;
    }

    public function setuser(?User $user): self
    {
        $this->id_user = $user;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(string $reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getDatePub(): ?\DateTimeInterface
    {
        return $this->date_pub;
    }

    public function setDatePub(\DateTimeInterface $date_pub): self
    {
        $this->date_pub = $date_pub;

        return $this;
    }

    public function getIdQuestion(): ?int
    {
        return $this->id_question;
    }

    public function setIdQuestion(int $id_question): self
    {
        $this->id_question = $id_question;

        return $this;
    }
    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }
    public function incrementLikes(): void
    {
        ++$this->likes;
    }
    public function getDislikes(): ?int
    {
        return $this->disliked;
    }
    public function incrementDislikes(): void
    {
        ++$this->disliked;
    }

    public function setDislikes(?int $dislikes): self
    {
        $this->disliked  = $dislikes;

        return $this;
    }
}
