<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[UniqueEntity('email')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\EntityListeners(['App\EntityListener\UserListener'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(min: 2, max: 100)]
    private string $prenom;

    #[ORM\Column(length: 100)]
    #[Assert\Length(min: 2, max: 100)]
    private string $nom;

    #[ORM\Column(length: 320, unique: true)]
    #[Assert\Email]
    #[Assert\Length(min: 2, max: 320)]
    private string $email;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull()]
    private string $adresse;

    #[ORM\Column]
    #[Assert\NotNull]
    private array $roles = [];

    private ?string $plainPassword = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
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

    public function __construct()
    {
        $this->problematiques = new ArrayCollection();
        $this->date_ajout = new \DateTimeImmutable();
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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return void
     * @throws \Exception
     */
    public function updateDateModif(): void
    {
        $this->date_modif = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
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

    /**
     * @see UserInterface
     */
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
}
