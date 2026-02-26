<?php

namespace App\Services;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TopicService
{
    public function __construct(
        private TopicRepository $topicRepository,
        private CacheInterface $cache,
        private EntityManagerInterface $em,
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function getAllTopics(): array
    {
        return $this->cache->get('topics_list', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            return $this->topicRepository->findAll();
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    public function create(string $name): Topic
    {
        $topic = new Topic();
        $topic->setName($name);

        $this->em->persist($topic);
        $this->em->flush();

        $this->cache->delete('topics_list');

        return $topic;
    }
}
