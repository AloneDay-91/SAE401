<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserDevoirVoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserDevoirVoteRepository::class)]
#[ApiResource]
class UserDevoirVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $vote = null;

    #[ORM\Column]
    private ?bool $verif = null;

    #[ORM\ManyToOne(inversedBy: 'id_userDevoirVote')]
    private ?Devoirs $devoirs = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userDevoirVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVote(): ?int
    {
        return $this->vote;
    }

    public function setVote(int $vote): static
    {
        $this->vote = $vote;

        return $this;
    }

    public function isVerif(): ?bool
    {
        return $this->verif;
    }

    public function setVerif(bool $verif): static
    {
        $this->verif = $verif;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
