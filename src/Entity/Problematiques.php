<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\SuiviProblematique;
use App\Repository\ProblematiquesRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: ProblematiquesRepository::class)]
#[Vich\Uploadable]
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

    #[Vich\UploadableField(mapping: 'problematiques_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date_ajout = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date_modif = null;

    /* #[ORM\OneToOne(targetEntity: SuiviProblematique::class, mappedBy: 'problematique', cascade: ['persist', 'remove'])]
    private $suiviProblematiques; */

    #[ORM\OneToOne(targetEntity: SuiviProblematique::class, mappedBy: 'problematique', cascade: ['remove'])]
    private $suiviProblematiques;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'problematiques')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private $auteur = null;

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


    /**
     * Get the value of suiviProblematiques
     */
    public function getSuiviProblematiques(): ?SuiviProblematique
    {
        return $this->suiviProblematiques;
    }

    /**
     * Set the value of suiviProblematiques
     *
     * @return  self
     */
    public function setSuiviProblematiques(?SuiviProblematique $suiviProblematiques): self
    {
        // unset the owning side of the relation if necessary
        if ($suiviProblematiques === null && $this->suiviProblematiques !== null) {
            $this->suiviProblematiques->setProblematique(null);
        }

        // set the owning side of the relation if necessary
        if ($suiviProblematiques !== null && $suiviProblematiques->getProblematique() !== $this) {
            $suiviProblematiques->setProblematique($this);
        }

        $this->suiviProblematiques = $suiviProblematiques;

        return $this;
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

    public function getId(): ?int
    {
        return $this->id;
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
     * Get the value of commentaire
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set the value of commentaire
     *
     * @return  self
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}
