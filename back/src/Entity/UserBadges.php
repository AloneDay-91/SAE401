<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserBadgesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserBadgesRepository::class)]
#[ApiResource]
class UserBadges
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'id_userBadges')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userBadges')]
    private ?Badges $id_badges = null;

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

    public function getIdBadges(): ?Badges
    {
        return $this->id_badges;
    }

    public function setIdBadges(?Badges $id_badges): static
    {
        $this->id_badges = $id_badges;

        return $this;
    }
}
