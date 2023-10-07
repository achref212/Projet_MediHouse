<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(nullable: true)]

    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?User $id_user = null;
    /**
     * @Assert\NotBlank(message="champs obligatoire")
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $question = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_pub = null;

    #[ORM\Column(nullable: true)]
    private ?int $likes = null;



    #[ORM\Column(nullable: true)]
    private ?int $dislikes = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;
    public function getuser(): ?User
    {
        return $this->id_user;
    }

    public function setuser(?User $user): self
    {
        $this->id_user = $user;

        return $this;
    }

    #[ORM\Column(nullable: true)]
    private ?bool $hide_name = null;
    public function getId(): ?int
    {
        return $this->id;
    }


    public function __construct()
    {
        $this->date_pub = new \DateTime();
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

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
        return $this->dislikes;
    }
    public function incrementDislikes(): void
    {
        ++$this->dislikes;
    }

    public function setDislikes(?int $dislikes): self
    {
        $this->dislikes = $dislikes;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getHideName(): ?bool
    {
        return $this->hide_name;
    }

    public function setHideName(bool $hide_name): self
    {
        $this->hide_name = $hide_name;

        return $this;
    }
}
