<?php

namespace App\Controller\Api;

use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/api')]
class ApiTopicController extends AbstractController
{
    #[Route('/topics', name: 'api_topics', methods: ['GET'])]
    public function topics(TopicRepository $topicRepository, CacheInterface $cache): JsonResponse
    {
        $topics = $cache->get('topics_list', function (ItemInterface $item) use ($topicRepository) {
            $item->expiresAfter(3600);
            return $topicRepository->findAll();
        });

        return $this->json(array_map(fn($topic) => [
            'id' => $topic->getId(),
            'name' => $topic->getName(),
        ], $topics));
    }
}
