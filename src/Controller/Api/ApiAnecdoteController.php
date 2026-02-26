<?php

namespace App\Controller\Api;

use App\Entity\Anecdote;
use App\Services\RabbitMQ\AnecdotePublisherService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiAnecdoteController extends AbstractController
{
    #[Route('/anecdotes', name: 'api_anecdotes', methods: ['GET'])]
    public function index(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $topicId = $request->query->get('topic');

        $anecdotes = $topicId
            ? $em->getRepository(Anecdote::class)->findBy(['topic' => $topicId])
            : $em->getRepository(Anecdote::class)->findAll();

        $data = [];
        foreach ($anecdotes as $a) {
            $data[] = [
                'id' => $a->getId(),
                'text' => $a->getText(),
                'topic' => $a->getTopic()?->getName(),
                'votesSum' => $a->getVotesSum(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/anecdotes', name: 'api_anecdotes_create', methods: ['POST'])]
    public function create(Request $request, AnecdotePublisherService $publisher): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        // Support both JSON and form data
        $contentType = $request->headers->get('Content-Type');
        if (str_contains($contentType, 'application/json')) {
            $body = json_decode($request->getContent(), true);
            $text = $body['text'] ?? null;
            $topicId = isset($body['topicId']) ? (int)$body['topicId'] : null;
        } else {
            $text = $request->request->get('text');
            $topicId = $request->request->getInt('topicId') ?: null;
        }

        if (!$text) {
            return $this->json(['success' => false, 'error' => 'Missing text'], 400);
        }

        $publisher->publish($text, $user->getId(), $topicId);

        return $this->json(['success' => true, 'message' => 'Anecdote sent to queue']);
    }
}
