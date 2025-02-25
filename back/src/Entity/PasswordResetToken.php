<?php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PasswordResetTokenRepository;

#[ApiResource]
#[ORM\Entity(repositoryClass: PasswordResetTokenRepository::class)]
class PasswordResetToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $token;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private User $user;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $expiresAt;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->token = bin2hex(random_bytes(32)); // Générer un token unique
        $this->expiresAt = new \DateTime('+1 hour'); // Expiration après 1 heure
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isExpired(): bool
    {
        return new \DateTime() > $this->expiresAt;
    }
}
