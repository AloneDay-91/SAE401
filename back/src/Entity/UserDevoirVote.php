<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\UserDevoirVoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[
ORM\Entity(repositoryClass: UserDevoirVoteRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['userDevoirVote:read']],
            forceEager: false,
        ),
        new Post(
            denormalizationContext: ['groups' => ['userDevoirVote:write']],
            forceEager: false,
        ),
        new Patch(
            denormalizationContext: ['groups' => ['userDevoirVote:write']],
            forceEager: false,
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['userDevoirVote:read']],
    denormalizationContext: ['groups' => ['userDevoirVote:write']]
)]
class UserDevoirVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['userDevoirVote:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['userDevoirVote:read', 'userDevoirVote:write'])]
    private ?int $vote = null;

    #[ORM\Column]
    #[Groups(['userDevoirVote:read', 'userDevoirVote:write'])]
    private ?bool $verif = null;

    #[ORM\ManyToOne(inversedBy: 'userDevoirVotes', fetch: 'EAGER')]
    #[Groups(['userDevoirVote:read', 'userDevoirVote:write'])]
    private ?Devoirs $devoirs = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userDevoirVotes', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['userDevoirVote:read', 'userDevoirVote:write'])]
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
