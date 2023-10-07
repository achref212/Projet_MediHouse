<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use phpDocumentor\Reflection\Type;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

#[ORM\Entity(repositoryClass: UserRepository::class)]


#[Vich\Uploadable]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("user")]
    private ?string $nom = null;



    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Groups("user")]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Groups("user")]
    private ?string $genre = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Groups("user")]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Groups("user")]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, type: 'string')]
    #[Assert\NotBlank(message: "Please, upload the photo.")]
    private $profilepicture;

    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'profilepicture')]
    private ?File $imgFile = null;

    #[ORM\Column(type: 'boolean')]
    private $activate = true;

    #[ORM\Column(length: 180, unique: true)]

    /* #[Assert\Regex(
        pattern: "/^[^\s@]+@[^\s@]+\.(com|tn|fr)$/",
        message: "L'adresse email doit être au format exemple@domaine.com, exemple@domaine.tn ou exemple@domaine.fr"
    )]*/
    #[Groups("user")]
    private ?string $email = null;

    #[ORM\Column]

    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    /* #[Assert\Regex(
        pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$/",
        message: "Le mot de passe doit contenir au moins 8 caractères dont une majuscule, une minuscule, un chiffre et un caractère spécial"
    )]*/
    private ?string $password = null;

    #[ORM\Column]
    private ?string $reset_token;

    #[ORM\OneToMany(mappedBy: 'Patient', targetEntity: Fiche::class)]
    private Collection $fiches;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: RendezVous::class)]
    private Collection $rendezVouses;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    //#[Assert\LessThanOrEqual("2005-12-31", message: "La date de naissance doit être avant le 31/12/2005")]
    private ?\DateTimeInterface $date_naissance = null;

    public function __construct()
    {

        $this->fiches = new ArrayCollection();
        $this->rendezVouses = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }
    public function getImgFile(): ?File
    {
        return $this->imgFile;
    }
    public function getProfilepicture(): ?string
    {
        return $this->profilepicture;
    }


    public function setProfilepicture(?string $profilepicture): self
    {
        $this->profilepicture = $profilepicture;

        return $this;
    }
    public function setImgFile(File $imgFile): self
    {
        $this->imgFile = $imgFile;
        return $this;
    }
    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }
    public function getadresse(): ?string
    {
        return $this->adresse;
    }

    public function setadresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }
    /**
     * @return Collection<int, Fiche>
     */
    public function getFiches(): Collection
    {
        return $this->fiches;
    }

    public function addFich(Fiche $fich): self
    {
        if (!$this->fiches->contains($fich)) {
            $this->fiches->add($fich);
            $fich->setPatient($this);
        }

        return $this;
    }

    public function removeFich(Fiche $fich): self
    {
        if ($this->fiches->removeElement($fich)) {
            // set the owning side to null (unless already changed)
            if ($fich->getPatient() === $this) {
                $fich->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVouses(): Collection
    {
        return $this->rendezVouses;
    }

    public function addRendezVouse(RendezVous $rendezVouse): self
    {
        if (!$this->rendezVouses->contains($rendezVouse)) {
            $this->rendezVouses->add($rendezVouse);
            $rendezVouse->setPatient($this);
        }

        return $this;
    }

    public function removeRendezVouse(RendezVous $rendezVouse): self
    {
        if ($this->rendezVouses->removeElement($rendezVouse)) {
            // set the owning side to null (unless already changed)
            if ($rendezVouse->getPatient() === $this) {
                $rendezVouse->setPatient(null);
            }
        }

        return $this;
    }
    public function getprenom(): ?string
    {
        return $this->prenom;
    }
    public function getnom(): ?string
    {
        return $this->nom;
    }


    public function setprenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }
    public function setnom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getActivate(): ?bool
    {
        return $this->activate;
    }

    public function setActivate(bool $activate): self
    {
        $this->activate = $activate;

        return $this;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }



    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /**
     * @return mixed
     */
    public function getResetToken()
    {
        return $this->reset_token;
    }

    /**
     * @param mixed $reset_token
     */
    public function setResetToken($reset_token): void
    {
        $this->reset_token = $reset_token;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(?\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }
}
