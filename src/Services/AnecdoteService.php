<?php

namespace App\Services;

use App\Entity\Anecdote;
use App\Entity\Topic;
use Doctrine\ORM\EntityManagerInterface;

class AnecdoteService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    /**
     * Получить все анекдоты (или по топикам)
     * Всегда возвращает массив Entity объектов
     */
    public function getAnecdotes(array $topicIds = []): array
    {
        if ($topicIds) {
            $dql = "
                SELECT a FROM App\Entity\Anecdote a
                JOIN a.topics t
                WHERE t.id IN (:topicIds)
                GROUP BY a.id
                HAVING COUNT(DISTINCT t.id) = :topicsCount
            ";

            $query = $this->em->createQuery($dql)
                ->setParameter('topicIds', $topicIds)
                ->setParameter('topicsCount', count($topicIds));

            return $query->getResult();
        }

        return $this->em->getRepository(Anecdote::class)->findBy([], ['id' => 'ASC']);
    }

    /**
     * Получить последний ID анекдота
     */
    public function getLatestId(): int
    {
        $latest = $this->em->getRepository(Anecdote::class)->findOneBy([], ['id' => 'DESC']);
        return $latest?->getId() ?? 0;
    }

    /**
     * Получить все топики
     */
    public function getAllTopics(): array
    {
        return $this->em->getRepository(Topic::class)->findAll();
    }

    /**
     * Получить анекдоты по одному топику (для HTML страниц)
     */
    public function getAnecdotesByTopic(int $topicId): array
    {
        return $this->getAnecdotes([$topicId]);
    }

    /**
     * Получить все анекдоты (для HTML страниц)
     */
    public function getAllAnecdotes(): array
    {
        return $this->getAnecdotes();
    }
}
