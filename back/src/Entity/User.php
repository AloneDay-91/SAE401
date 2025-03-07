<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(security: "is_granted('ROLE_ADMIN')")]
#[Get(security: "is_granted('ROLE_ADMIN') or object == user")]
#[GetCollection(security: "is_granted('ROLE_ADMIN')")]
#[Post(security: "is_granted('ROLE_ADMIN')")]
#[Put(security: "is_granted('ROLE_ADMIN') or object == user")]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
#[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $avatar = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $roleapp = null;

    // Relation avec Classes (ManyToOne)
    #[ORM\ManyToOne(targetEntity: Classes::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read', 'user:write'])]
    private ?Classes $id_classes = null;

    // Relation avec Badges via la table pivot UserBadges (OneToMany)
    #[ORM\OneToMany(targetEntity: UserBadges::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'user:write'])]
    private Collection $userBadges;

    // Relation avec CheckboxStatus (OneToMany)
    #[ORM\OneToMany(targetEntity: CheckboxStatus::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'user:write'])]
    private Collection $checkboxStatuses;

    // Relation avec Devoirs (OneToMany)
    #[ORM\OneToMany(targetEntity: Devoirs::class, mappedBy: 'id_users', cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'user:write'])]
    private Collection $devoirs;

    // Relation avec userDevoirVote (OneToMany)
    #[ORM\OneToMany(targetEntity: UserDevoirVote::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'user:write'])]
    private Collection $userDevoirVotes;


    public function __construct()
    {
        $this->userBadges = new ArrayCollection();
        $this->checkboxStatuses = new ArrayCollection();
        $this->devoirs = new ArrayCollection();
        $this->userDevoirVotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getRoleapp(): ?string
    {
        return $this->roleapp;
    }

    public function setRoleapp(string $roleapp): static
    {
        $this->roleapp = $roleapp;

        return $this;
    }

    // Relation avec Classes
    public function getIdClasses(): ?Classes
    {
        return $this->id_classes;
    }

    public function setIdClasses(?Classes $id_classes): static
    {
        $this->id_classes = $id_classes;

        return $this;
    }

    // Relation avec UserBadges
    /**
     * @return Collection<int, UserBadges>
     */
    public function getUserBadges(): Collection
    {
        return $this->userBadges;
    }

    public function addUserBadge(UserBadges $userBadge): static
    {
        if (!$this->userBadges->contains($userBadge)) {
            $this->userBadges[] = $userBadge;
            $userBadge->setUser($this);
        }

        return $this;
    }

    public function removeUserBadge(UserBadges $userBadge): static
    {
        if ($this->userBadges->removeElement($userBadge)) {
            if ($userBadge->getUser() === $this) {
                $userBadge->setUser(null);
            }
        }

        return $this;
    }

    // Relation avec CheckboxStatus
    /**
     * @return Collection<int, CheckboxStatus>
     */
    public function getCheckboxStatuses(): Collection
    {
        return $this->checkboxStatuses;
    }

    public function addCheckboxStatus(CheckboxStatus $checkboxStatus): static
    {
        if (!$this->checkboxStatuses->contains($checkboxStatus)) {
            $this->checkboxStatuses[] = $checkboxStatus;
            $checkboxStatus->setUser($this);
        }

        return $this;
    }

    public function removeCheckboxStatus(CheckboxStatus $checkboxStatus): static
    {
        if ($this->checkboxStatuses->removeElement($checkboxStatus)) {
            if ($checkboxStatus->getUser() === $this) {
                $checkboxStatus->setUser(null);
            }
        }

        return $this;
    }

    // Relation avec Devoirs
    /**
     * @return Collection<int, Devoirs>
     */
    public function getDevoirs(): Collection
    {
        return $this->devoirs;
    }

    public function getRoles(): array
    {
        return [$this->roleapp];
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    // Relation avec UserDevoirVote
    /**
     * @return Collection<int, UserDevoirVote>
     */
    public function getUserDevoirVotes(): Collection
    {
        return $this->userDevoirVotes;
    }

    public function addUserDevoirVote(UserDevoirVote $userDevoirVote): static
    {
        if (!$this->userDevoirVotes->contains($userDevoirVote)) {
            $this->userDevoirVotes[] = $userDevoirVote;
            $userDevoirVote->setIdUser($this);
        }

        return $this;
    }

    public function removeUserDevoirVote(UserDevoirVote $userDevoirVote): static
    {
        if ($this->userDevoirVotes->removeElement($userDevoirVote)) {
            if ($userDevoirVote->getIdUser() === $this) {
                $userDevoirVote->setIdUser(null);
            }
        }

        return $this;
    }
}