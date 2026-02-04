<?php

namespace App\Entity;

use App\Repository\AnecdoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnecdoteRepository::class)]
class Anecdote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    private ?string $text = null;

    #[ORM\ManyToOne(targetEntity: Topic::class, inversedBy: "anecdotes")]
    #[ORM\JoinColumn(name: "topic_id", referencedColumnName: "id", nullable: false)]
    private ?Topic $topic = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "anecdotes")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: "anecdote", targetEntity: Vote::class, orphanRemoval: true)]
    private Collection $votes;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(Topic $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function getVotesSum(): int
    {
        return count($this->votes);
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

    public function addVote(Vote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setAnecdote($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): static
    {
        if ($this->votes->removeElement($vote)) {
            if ($vote->getAnecdote() === $this) {
                $vote->setAnecdote(null);
            }
        }

        return $this;
    }
}
