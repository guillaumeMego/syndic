<?php

namespace App\Entity;

use App\Entity\User;
use App\Enum\EtatProblematiqueEnum;
use App\Repository\SuiviProblematiqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuiviProblematiqueRepository::class)]
class SuiviProblematique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $etat = EtatProblematiqueEnum::EN_ATTENTE;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date_modif = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $membreValidateur;

    #[ORM\OneToOne(targetEntity: Problematiques::class, inversedBy: 'suiviProblematique', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $problematique;


    public function __construct()
    {
        $this->date_modif = new \DateTimeImmutable();
    }

    public function getProblematique(): ?Problematiques
    {
        return $this->problematique;
    }

    public function setProblematique(?Problematiques $problematique): self
    {
        $this->problematique = $problematique;

        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

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
     * Get the value of membreValidateur
     */
    public function getMembreValidateur()
    {
        return $this->membreValidateur;
    }

    /**
     * Set the value of membreValidateur
     *
     * @return  self
     */
    public function setMembreValidateur($membreValidateur)
    {
        $this->membreValidateur = $membreValidateur;

        return $this;
    }

    public function __toString()
    {
        return $this->etat;
    }
}
