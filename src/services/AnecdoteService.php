<?php

namespace App\Services;

use App\Entity\Anecdote;
use Doctrine\ORM\EntityManagerInterface;

class AnecdoteService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function updateText(int $id, string $text): void
    {
        $anecdote = $this->em->getRepository(Anecdote::class)->find($id);

        if (!$anecdote) {
            throw new \Exception('Not found');
        }

        $anecdote->setText($text);
        $this->em->flush();
    }
}
