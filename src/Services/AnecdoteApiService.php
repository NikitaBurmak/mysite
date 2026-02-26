<?php

namespace App\Services;

use App\Entity\Anecdote;
use App\Entity\User;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\TopicService;
use App\Services\RabbitMQ\AnecdotePublisherService;

class AnecdoteApiService
{

    public function __construct(
        private EntityManagerInterface   $em,
        private TopicRepository          $topicRepository,
        private TopicService             $topicService,
        private AnecdotePublisherService $publisher
    )
    {
    }

    public function createAnecdote(User $user, string $text, ?int $topicId, ?int $currentTopicId): void
    {
        $text = trim($text);
        if (!$text) {
            throw new \DomainException('Empty text');
        }

        $resolvedTopicId = $currentTopicId ?: $topicId;

        $this->publisher->publish($text, $user->getId(), $resolvedTopicId);
    }

    public function getAllAnecdotes(): array
    {
        return $this->em->getRepository(Anecdote::class)->findBy([], ['id' => 'ASC']);
    }

    public function getAllTopics(): array
    {
        return $this->topicService->getAllTopics();
    }

    public function getAnecdotesByTopic(int $topicId): array
    {
        return $this->em->getRepository(Anecdote::class)
            ->findBy(['topic' => $topicId], ['id' => 'ASC']);
    }
}
