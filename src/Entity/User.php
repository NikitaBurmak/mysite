<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity()]
#[ORM\Table(name: "app_user")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: "string")]
    private string $password;

    #[ORM\Column(nullable: true)]
    private ?string $nickname = null;

    #[ORM\Column(nullable: true)]
    private ?string $avatarPath = null;

    #[ORM\OneToMany(targetEntity: Anecdote::class, mappedBy: "user")]
    private Collection $anecdotes;

    public function __construct()
    {
        $this->anecdotes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getRoles(): array
    {
        return array_unique(array_merge($this->roles, ['ROLE_USER']));
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }

    // ===========================
    public function getAnecdotes(): Collection
    {
        return $this->anecdotes;
    }

    public function addAnecdote(Anecdote $anecdote): static
    {
        if (!$this->anecdotes->contains($anecdote)) {
            $this->anecdotes->add($anecdote);
            $anecdote->setUser($this);
        }
        return $this;
    }

    public function removeAnecdote(Anecdote $anecdote): static
    {
        if ($this->anecdotes->removeElement($anecdote)) {
            if ($anecdote->getUser() === $this) {
                $anecdote->setUser(null);
            }
        }
        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;
        return $this;
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    public function setAvatarPath(?string $avatarPath): self
    {
        $this->avatarPath = $avatarPath;
        return $this;
    }
}
