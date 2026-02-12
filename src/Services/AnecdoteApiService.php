<?php

namespace App\Services;

use App\Entity\Anecdote;
use App\Entity\User;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\TopicService;
class AnecdoteApiService
{
    public function __construct(
        private EntityManagerInterface $em,
        private TopicRepository $topicRepository,
        private TopicService $topicService
    ) {}

    public function createAnecdote(User $user, string $text, int $topicId): Anecdote
    {
        if (mb_strlen($text) < 1) {
            throw new \DomainException('Пустой анекдот');
        }

        $topic = $this->topicRepository->find($topicId);
        if (!$topic) {
            throw new \DomainException('Тема не найдена');
        }

        $anecdote = new Anecdote();
        $anecdote->setText($text);
        $anecdote->setTopic($topic);
        $anecdote->setUser($user);

        $this->em->persist($anecdote);
        $this->em->flush();

        return $anecdote;
    }

    public function getAllAnecdotes(): array
    {
        return $this->em->getRepository(Anecdote::class)->findAll();
    }
    public function getAllTopics(): array
    {
        return $this->topicService->getAllTopics();
    }
}
