<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\DevoirsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: DevoirsRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['devoir:read']],
    denormalizationContext: ['groups' => ['devoir:write']],
)]
#[ApiFilter(SearchFilter::class, properties: ['id_users' => 'exact'])]
class Devoirs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['devoir:read', 'userDevoirVote:read', 'userDevoirVote:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['devoir:read', 'devoir:write', 'userDevoirVote:read'])]
    private ?string $intitule = null;

    #[ORM\Column(length: 255)]
    #[Groups(['devoir:read', 'devoir:write'])]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['devoir:read', 'devoir:write', 'userDevoirVote:read'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['devoir:read', 'devoir:write', 'userDevoirVote:read'])]
    private ?\DateTimeInterface $heure = null;

    #[ORM\Column(length: 255)]
    #[Groups(['devoir:read', 'devoir:write'])]
    private ?string $status = null;

    // Relation avec Users (ManyToOne)
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'devoirs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['devoir:read', 'devoir:write'])]
    private ?User $id_users = null;

    // Relation avec Matieres (ManyToOne)
    #[ORM\ManyToOne(targetEntity: Matieres::class, inversedBy: 'devoirs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['devoir:read', 'devoir:write', 'userDevoirVote:read'])]
    private ?Matieres $id_matieres = null;

    // Relation avec Categories (ManyToOne)
    #[ORM\ManyToOne(targetEntity: Categories::class, inversedBy: 'devoirs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['devoir:read', 'devoir:write', 'userDevoirVote:read'])]
    private ?Categories $id_categories = null;

    // Relation avec FormatRendu (ManyToOne)
    #[ORM\ManyToOne(targetEntity: FormatRendu::class, inversedBy: 'devoirs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['devoir:read', 'devoir:write', 'format_rendu:write'])]
    private ?FormatRendu $id_formatRendu = null;

    // Relation avec CheckboxStatus (OneToMany)
    #[ORM\OneToMany(mappedBy: 'devoirs', targetEntity: CheckboxStatus::class, cascade: ['persist', 'remove'])]
    #[Groups(['devoir:read', 'devoir:write'])]
    private Collection $checkboxStatuses;

    // Relation avec UserDevoirVote (OneToMany)
    #[ORM\OneToMany(mappedBy: 'devoirs', targetEntity: UserDevoirVote::class, cascade: ['persist', 'remove'])]
    #[Groups(['devoir:read', 'devoir:write', 'userDevoirVote:read', 'userDevoirVote:write'])]
    private Collection $userDevoirVotes;

    #[ORM\ManyToOne(targetEntity: Classes::class, inversedBy: 'devoirs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['devoir:read', 'devoir:write', 'userDevoirVote:read'])]
    private ?Classes $id_classes = null;

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

    // Pour la relation Categories
    public function getIdCategories(): ?Categories
    {
        return $this->id_categories;
    }

    public function setIdCategories(?Categories $id_categories): static
    {
        $this->id_categories = $id_categories;

        return $this;
    }

// Pour la relation FormatRendu
    public function getIdFormatRendu(): ?FormatRendu
    {
        return $this->id_formatRendu;
    }

    public function setIdFormatRendu(?FormatRendu $id_formatRendu): static
    {
        $this->id_formatRendu = $id_formatRendu;

        return $this;
    }

    public function getIdClasses(): ?Classes
    {
        return $this->id_classes;
    }

    public function setIdClasses(?Classes $id_classes): static
    {
        $this->id_classes = $id_classes;

        return $this;
    }

}