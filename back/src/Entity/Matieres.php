<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MatieresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MatieresRepository::class)]
#[ApiResource]
class Matieres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['devoir:read', 'devoir:write', 'userDevoirVote:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['devoir:read', 'devoir:write', 'userDevoirVote:read'])]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Groups(['devoir:read', 'devoir:write'])]
    private ?string $couleur = null;

    /**
     * @var Collection<int, Devoirs>
     */
    #[ORM\OneToMany(targetEntity: Devoirs::class, mappedBy: 'id_matieres')]
    private Collection $devoirs;

    public function __construct()
    {
        $this->devoirs = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(string $couleur): static
    {
        $this->couleur = $couleur;

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
            $devoir->setIdMatieres($this);
        }

        return $this;
    }

    public function removeDevoir(Devoirs $devoir): static
    {
        if ($this->devoirs->removeElement($devoir)) {
            // set the owning side to null (unless already changed)
            if ($devoir->getIdMatieres() === $this) {
                $devoir->setIdMatieres(null);
            }
        }

        return $this;
    }
}
