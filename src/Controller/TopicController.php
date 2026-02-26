<?php

namespace App\Controller;

use App\Repository\TopicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\Services\TopicService;
use App\Entity\Topic;
use Symfony\Component\HttpFoundation\Request;

class TopicController extends AbstractController
{
    public function __construct(private TopicService $topicService)
    {
    }

    #[Route('/topics', name: 'topic_index')]
    public function index(): Response
    {
        $topics = $this->topicService->getAllTopics();
        return $this->render('topic/index.html.twig', ['topics' => $topics]);
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
