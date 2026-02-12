<?php

use App\Entity\Anecdote;
use Doctrine\ORM\EntityManagerInterface;

class AnecdotePublisherService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}
    public function create (string $text , int $authorId) : Anecdote
    {
        if (mb_strlen($text) < 10) {
            throw new \DomainException('Text too short');
        }

        $anecdote = new Anecdote();
        $anecdote->setText($text);
        $anecdote->setAuthorId($authorId);
        $anecdote->setStatus('pending');

        $this->em->persist($anecdote);
        $this->em->flush();

        return $anecdote;
    }
}
