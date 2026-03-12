<?php

namespace App\Controller\Api;

use App\Services\TopicService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiTopicController extends AbstractController
{
    public function __construct(private TopicService $topicService)
    {
    }

    #[Route('/topics', name: 'api_topics', methods: ['GET'])]
    public function topics(): JsonResponse
    {
        $topics = $this->topicService->getAllTopics();

        return $this->json(array_map(fn($topic) => [
            'id' => $topic->getId(),
            'name' => $topic->getName(),
        ], $topics));
    }
}
