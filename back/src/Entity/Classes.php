<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ClassesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ClassesRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['classes:read']],
    denormalizationContext: ['groups' => ['classes:write']],
)]
class Classes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['classes:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'classes:read', 'classes:write'])]
    private ?string $intitule = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'classes:read', 'classes:write'])]
    private ?string $promo = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'classes:read', 'classes:write'])]
    private ?string $tp = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'classes:read', 'classes:write'])]
    private ?string $td = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'id_classes')]
    private Collection $users;

    /**
     * @var Collection<int, Devoirs>
     */
    #[ORM\OneToMany(targetEntity: Devoirs::class, mappedBy: 'id_classes')]
    private Collection $devoirs;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->devoirs = new ArrayCollection();
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

    public function getPromo(): ?string
    {
        return $this->promo;
    }

    public function setPromo(string $promo): static
    {
        $this->promo = $promo;

        return $this;
    }

    public function getTp(): ?string
    {
        return $this->tp;
    }

    public function setTp(string $tp): static
    {
        $this->tp = $tp;

        return $this;
    }

    public function getTd(): ?string
    {
        return $this->td;
    }

    public function setTd(string $td): static
    {
        $this->td = $td;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setIdClasses($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getIdClasses() === $this) {
                $user->setIdClasses(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Devoirs>
     */
    public function getDevoirs(): Collection
    {
        return $this->devoirs;
    }

    public function addDevoir(Devoirs $devoir): static
    {
        if (!$this->devoirs->contains($devoir)) {
            $this->devoirs->add($devoir);
            $devoir->setIdClasses($this);
        }

        return $this;
    }

    public function removeDevoir(Devoirs $devoir): static
    {
        if ($this->devoirs->removeElement($devoir)) {
            // set the owning side to null (unless already changed)
            if ($devoir->getIdClasses() === $this) {
                $devoir->setIdClasses(null);
            }
        }

        return $this;
    }
}
