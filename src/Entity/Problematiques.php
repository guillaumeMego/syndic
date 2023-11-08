<?php

namespace App\Entity;

use App\Repository\ProblematiquesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;
use App\Entity\SuiviProblematique;


#[ORM\Entity(repositoryClass: ProblematiquesRepository::class)]
class Problematiques
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 127)]
    #[Assert\NotBlank]
    private ?string $problematique = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date_ajout = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date_modif = null;

    #[ORM\OneToOne(targetEntity: SuiviProblematique::class, mappedBy: 'problematique', cascade: ['persist', 'remove'])]
    private $suiviProblematiques;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="problematiques")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'problematiques')]
    #[ORM\JoinColumn(nullable: false)]
    private $auteur;

    // ...

    public function __construct()
    {
        $this->date_ajout = new \DateTimeImmutable();
        $this->date_modif = new \DateTimeImmutable();
    }


    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }


    public function addSuiviProblematique(SuiviProblematique $suiviProblematique): self
    {
        if (!$this->suiviProblematiques->contains($suiviProblematique)) {
            $this->suiviProblematiques[] = $suiviProblematique;
            $suiviProblematique->setProblematique($this);
        }

        return $this;
    }

    public function removeSuiviProblematique(SuiviProblematique $suiviProblematique): self
    {
        if ($this->suiviProblematiques->removeElement($suiviProblematique)) {
            // set the owning side to null (unless already changed)
            if ($suiviProblematique->getProblematique() === $this) {
                $suiviProblematique->setProblematique(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProblematique(): ?string
    {
        return $this->problematique;
    }

    public function setProblematique(string $problematique): static
    {
        $this->problematique = $problematique;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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
     * Get the value of suiviProblematiques
     */ 
    public function getSuiviProblematique()
    {
        return $this->suiviProblematiques;
    }
}
