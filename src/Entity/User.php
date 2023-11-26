<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[UniqueEntity('email')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\EntityListeners(['App\EntityListener\UserListener'])]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 100)]
    #[Assert\NotNull(message: "Le prénom ne peut pas être null.")]
    #[Assert\Length(min: 2, max: 100)]
    private string $prenom;

    #[ORM\Column(type: "string", length: 100)]
    #[Assert\NotNull(message: "Le nom ne peut pas être null.")]
    #[Assert\Length(min: 2, max: 100)]
    private string $nom;

    #[ORM\Column(type: "string", length: 320, unique: true)]
    #[Assert\Email]
    #[Assert\Length(min: 2, max: 320)]
    #[Assert\NotNull(message: "Le mail ne peut pas être null.")]
    private string $email;

    #[ORM\Column(type: "boolean")]
    private $isVerified = false;

    #[ORM\Column(type: "string", length: 10)]
    #[Assert\NotNull(message: "Le batiment ne peut pas être null.")]
    #[Assert\Choice(choices: ['A', 'B', 'C'], message: 'Veuillez choisir un bâtiment valide.')]
    private string $batiment;

    #[ORM\Column(type: "integer", length: 2)]
    #[Assert\NotNull(message: "L'étage ne peut pas être null.")]
    private int $etage;

    #[ORM\Column(type: "integer", length: 3)]
    #[Assert\NotNull(message: "Le numero d'appartement ne peut pas être null.")]
    private int $numeroAppartement;

    #[ORM\Column(type: "string", length: 10)]
    #[Assert\Length(min: 10, max: 10)]
    #[Assert\Regex(
        pattern: "/^0[1-9]([0-9]{2}){4}$/",
        message: "Le numéro de téléphone doit être un numéro français valide."
    )]
    private ?string $telephone = null;

    #[Vich\UploadableField(mapping: 'syndic_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(type: "json")]
    #[Assert\NotNull]
    private array $roles = [];

    private ?string $plainPassword = null;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank()]
    private string $password = 'password';

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull()]
    private \DateTimeImmutable $date_ajout;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull()]
    private \DateTimeImmutable $date_modif;

    #[ORM\OneToMany(targetEntity: Problematiques::class, mappedBy: 'auteur')]
    private $problematiques;

    private $roleChoice;

    #[ORM\Column(type: "boolean")]
    private $passwordChangeRequired;

    public function __construct()
    {
        $this->problematiques = new ArrayCollection();
        $this->date_ajout = new \DateTimeImmutable();
        $this->date_modif = new \DateTimeImmutable();
        $this->imageName = 'defaut.jpg';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getBatiment()
    {
        return $this->batiment;
    }

    public function setBatiment($batiment)
    {
        $this->batiment = $batiment;

        return $this;
    }

    public function getEtage()
    {
        return $this->etage;
    }

    public function setEtage($etage)
    {
        $this->etage = $etage;

        return $this;
    }

    public function getNumeroAppartement()
    {
        return $this->numeroAppartement;
    }

    public function setNumeroAppartement($numeroAppartement)
    {
        $this->numeroAppartement = $numeroAppartement;

        return $this;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->date_modif = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function removeRole(string $role): static
    {
        $key = array_search($role, $this->roles, true);
        if ($key !== false) {
            unset($this->roles[$key]);
        }

        return $this;
    }

    public function addRole(string $role): static
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }


    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getDateAjout(): ?\DateTimeImmutable
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTimeImmutable $date_ajout): static
    {
        $this->date_ajout = $date_ajout;

        return $this;
    }

    public function getDateModif(): ?\DateTimeImmutable
    {
        return $this->date_modif;
    }

    public function setDateModif(\DateTimeImmutable $date_modif): static
    {
        $this->date_modif = $date_modif;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return void
     * @throws \Exception
     */
    public function updateDateModif(): void
    {
        $this->date_modif = new \DateTimeImmutable();
    }

    public function getProblematiques(): Collection
    {
        return $this->problematiques;
    }

    public function addProblematique(Problematiques $problematique): self
    {
        if (!$this->problematiques->contains($problematique)) {
            $this->problematiques[] = $problematique;
            $problematique->setAuteur($this);
        }

        return $this;
    }

    public function removeProblematique(Problematiques $problematique): self
    {
        if ($this->problematiques->removeElement($problematique)) {
            // set the owning side to null (unless already changed)
            if ($problematique->getAuteur() === $this) {
                $problematique->setAuteur(null);
            }
        }

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

    public function getRoleChoice(): ?string
    {
        return $this->roleChoice;
    }

    public function setRoleChoice(string $roleChoice): self
    {
        $this->roleChoice = $roleChoice;

        // Mettez à jour les rôles en fonction du choix de rôle
        $this->roles = [$roleChoice];

        return $this;
    }

    public function getPasswordChangeRequired(): ?bool
    {
        return $this->passwordChangeRequired;
    }

    public function setPasswordChangeRequired(bool $passwordChangeRequired): self
    {
        $this->passwordChangeRequired = $passwordChangeRequired;

        return $this;
    }
}
