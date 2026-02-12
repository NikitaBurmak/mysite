<?php

namespace App\Services;

use App\Entity\Anecdote;
use App\Entity\User;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use Doctrine\ORM\EntityManagerInterface;

class VoteService
{
    public function __construct(
        private EntityManagerInterface $em,
        private VoteRepository         $voteRepo
    ) {}

    public function likeAnecdote(Anecdote $anecdote, User $user): int
    {
        $existingVote = $this->voteRepo->findOneBy([
            'anecdote' => $anecdote,
            'user' => $user
        ]);

        if ($existingVote) {
            return $this->voteRepo->count(['anecdote' => $anecdote]);
        }

        $vote = new Vote();
        $vote->setUser($user);
        $vote->setAnecdote($anecdote);

        $this->em->persist($vote);
        $this->em->flush();

        return $this->voteRepo->count(['anecdote' => $anecdote]);
    }
}
