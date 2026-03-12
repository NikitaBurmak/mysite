<?php

namespace App\Controller\Api;

use App\Services\AnecdoteService;
use App\Services\RabbitMQ\AnecdotePublisherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiAnecdoteController extends AbstractController
{
    public function __construct(
        private AnecdoteService $anecdoteService
    ) {}

    #[Route('/anecdotes', name: 'api_anecdotes', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $topicIds = $request->query->all('topics');

        if (!empty($topicIds)) {
            $topicIds = array_values(array_map(fn($id) => (int)$id, $topicIds));
        }

        $anecdotes = $this->anecdoteService->getAnecdotes($topicIds);

        $data = array_map(fn($a) => $a->toArray(), $anecdotes);

        return $this->json($data);
    }

    #[Route('/anecdotes', name: 'api_anecdotes_create', methods: ['POST'])]
    public function create(Request $request, AnecdotePublisherService $publisher): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $topicIds = [];

        $contentType = $request->headers->get('Content-Type');
        if ($contentType && str_contains($contentType, 'application/json')) {
            $body = json_decode($request->getContent(), true);
            $text = $body['text'] ?? null;
            $topicIds = array_map(fn($id) => (int)$id, $body['topicIds'] ?? []);
        } else {
            $text = $request->request->get('text');
            $topicIds = $request->request->all('topicIds');
        }

        if (!$text) {
            return $this->json(['success' => false, 'error' => 'Missing text'], 400);
        }

        $publisher->publish($text, $user->getId(), $topicIds);

        return $this->json(['success' => true, 'message' => 'Anecdote sent to queue']);
    }

    #[Route('/anecdotes/latest', name: 'api_anecdotes_latest', methods: ['GET'])]
    public function getLatestId(): JsonResponse
    {
        $latestId = $this->anecdoteService->getLatestId();

        return $this->json(['latestId' => $latestId]);
    }
}
