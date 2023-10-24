<?php

namespace App\Entity;

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
    private ?string $etat = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date_modif = null;

     /**
     * @ORM\ManyToOne(targetEntity=Problematiques::class, inversedBy="suiviProblematiques")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ORM\ManyToOne(targetEntity: Problematiques::class, inversedBy: 'suiviProblematiques')]
    #[ORM\JoinColumn(nullable: false)]
    private $problematique;

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
}
