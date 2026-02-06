<?php

namespace App\services;

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
    public function delete (int $id): void{
        $anecdote = $this->em->getRepository(Anecdote::class)->find($id);
        if (!$anecdote) {
            throw new \Exception('Not found');
        }
        $this->em->remove($anecdote);
        $this->em->flush();
    }


}
