<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CheckboxStatusRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CheckboxStatusRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['checkbox:read']],
    denormalizationContext: ['groups' => ['checkbox:write']],
)]
class CheckboxStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['checkbox:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'checkboxStatuses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['checkbox:read', 'checkbox:write'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Devoirs::class, inversedBy: 'checkboxStatuses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['checkbox:read', 'checkbox:write'])]
    private ?Devoirs $devoirs = null;

    #[ORM\Column]
    #[Groups(['checkbox:read', 'checkbox:write'])]
    private ?bool $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDevoirs(): ?Devoirs
    {
        return $this->devoirs;
    }

    public function setDevoirs(?Devoirs $devoirs): static
    {
        $this->devoirs = $devoirs;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }
}

