<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: "topics")]
class Topic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'topic', targetEntity: Anecdote::class)]
    private Collection $anecdotes;

    public function __construct()
    {
        $this->anecdotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, Anecdote>
     */
    public function getAnecdotes(): Collection
    {
        return $this->anecdotes;
    }

    public function addAnecdote(Anecdote $anecdote): static
    {
        if (!$this->anecdotes->contains($anecdote)) {
            $this->anecdotes->add($anecdote);
            $anecdote->setTopic($this);
        }

        return $this;
    }

    public function removeAnecdote(Anecdote $anecdote): static
    {
        if ($this->anecdotes->removeElement($anecdote)) {
            if ($anecdote->getTopic() === $this) {
                $anecdote->setTopic(null);
            }
        }

        return $this;
    }
}
