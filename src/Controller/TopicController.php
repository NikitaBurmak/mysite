<?php

namespace App\Controller;

use App\Services\TopicService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{
    public function __construct(private TopicService $topicService)
    {
    }

    #[Route('/topic/add', name: 'topic_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $name = trim($data['name'] ?? '');

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['requireLogin' => true], 401);
        }

        if (!$name) {
            return $this->json([
                'success' => false,
                'error' => 'Название темы пустое'
            ], 400);
        }

        $topic = $this->topicService->create($name);

        return $this->json([
            'success' => true,
            'id' => $topic->getId(),
            'name' => $topic->getName(),
        ]);
    }
}
