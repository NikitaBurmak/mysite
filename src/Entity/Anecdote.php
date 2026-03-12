<?php

namespace App\Entity;

use App\Repository\AnecdoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Anecdote
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    private ?string $text = null;

    #[ORM\ManyToMany(targetEntity: Topic::class, inversedBy: 'anecdotes')]
    #[ORM\JoinTable(name: 'anecdote_topic')]
    private Collection $topics;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'anecdotes')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'anecdote', targetEntity: Vote::class)]
    private Collection $votes;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // --- Topics ---
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): static
    {
        if (!$this->topics->contains($topic)) {
            $this->topics->add($topic);
            $topic->addAnecdote($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): static
    {
        if ($this->topics->removeElement($topic)) {
            $topic->removeAnecdote($this);
        }

        return $this;
    }

    // --- Votes ---
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function getVotesSum(): int
    {
        return count($this->votes);
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
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

    /**
     * Преобразовать в массив для API/JSON
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'topics' => array_map(fn($t) => $t->getName(), $this->topics->toArray()),
            'votesSum' => $this->getVotesSum(),
        ];
    }
}
