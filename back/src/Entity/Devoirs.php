<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DevoirsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevoirsRepository::class)]
#[ApiResource]
class Devoirs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $intitule = null;

    #[ORM\Column(length: 255)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heure = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    // Relation avec Users (ManyToOne)
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'devoirs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $id_users = null;

    // Relation avec Matieres (ManyToOne)
    #[ORM\ManyToOne(targetEntity: Matieres::class, inversedBy: 'devoirs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Matieres $id_matieres = null;

    // Relation avec Categories (ManyToOne)
    #[ORM\ManyToOne(targetEntity: Categories::class, inversedBy: 'devoirs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $id_categories = null;

    // Relation avec FormatRendu (ManyToOne)
    #[ORM\ManyToOne(targetEntity: FormatRendu::class, inversedBy: 'devoirs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FormatRendu $id_formatRendu = null;

    // Relation avec CheckboxStatus (OneToMany)
    #[ORM\OneToMany(mappedBy: 'devoirs', targetEntity: CheckboxStatus::class, cascade: ['persist', 'remove'])]
    private Collection $checkboxStatuses;

    // Relation avec UserDevoirVote (OneToMany)
    #[ORM\OneToMany(mappedBy: 'devoirs', targetEntity: UserDevoirVote::class, cascade: ['persist', 'remove'])]
    private Collection $userDevoirVotes;

    public function __construct()
    {
        $this->checkboxStatuses = new ArrayCollection();
        $this->userDevoirVotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): static
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(\DateTimeInterface $heure): static
    {
        $this->heure = $heure;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getIdUsers(): ?User
    {
        return $this->id_users;
    }

    public function setIdUsers(?User $id_users): static
    {
        $this->id_users = $id_users;

        return $this;
    }

    public function getIdMatieres(): ?Matieres
    {
        return $this->id_matieres;
    }


    public function setIdMatieres(?Matieres $id_matieres): static
    {
        $this->id_matieres = $id_matieres;

        return $this;
    }
}