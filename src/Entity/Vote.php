<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
#[ORM\UniqueConstraint(name: "user_anecdote_unique", columns: ["user_id", "anecdote_id"])]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Anecdote::class, inversedBy: "votes")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Anecdote $anecdote = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getAnecdote(): ?Anecdote
    {
        return $this->anecdote;
    }

    public function setAnecdote(Anecdote $anecdote): self
    {
        $this->anecdote = $anecdote;
        return $this;
    }
}
