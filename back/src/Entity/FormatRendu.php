<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FormatRenduRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FormatRenduRepository::class)]
#[ApiResource(
    denormalizationContext: ['groups' => ['format_rendu:write']]
)]
class FormatRendu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['format_rendu:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['format_rendu:write'])]
    private ?string $intitule = null;

    #[ORM\Column(length: 255)]
    #[Groups(['format_rendu:write'])]
    private ?string $lien = null;

    /**
     * @var Collection<int, Devoirs>
     */
    #[ORM\OneToMany(targetEntity: Devoirs::class, mappedBy: 'id_formatRendu')]
    private Collection $devoirs;

    public function __construct()
    {
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

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): static
    {
        $this->lien = $lien;

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
            $devoir->setIdFormatRendu($this);
        }

        return $this;
    }

    public function removeDevoir(Devoirs $devoir): static
    {
        if ($this->devoirs->removeElement($devoir)) {
            // set the owning side to null (unless already changed)
            if ($devoir->getIdFormatRendu() === $this) {
                $devoir->setIdFormatRendu(null);
            }
        }

        return $this;
    }
}
