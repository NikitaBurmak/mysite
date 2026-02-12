<?php

namespace App\Services;

use App\Repository\TopicRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TopicService
{
    public function __construct(
        private TopicRepository $topicRepository,
        private CacheInterface $cache
    ) {}

    public function getAllTopics(): array
    {
        return $this->cache->get('topics_list', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            return $this->topicRepository->findAll();
        });
    }
}
